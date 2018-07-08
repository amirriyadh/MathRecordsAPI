<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Record;
use App\History;
use App\Fork;

class RecordsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //display all records related to the user
        $user =  Auth::user();
        $records = Record::select('id','value','freeze','created_at','updated_at')
            ->where('user_id',$user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($records as $record){
            $reference = Fork::where('forking_id',$record->id)->first();
            if($reference != null) $record['reference'] = $reference->forked_id ;
        }
        return $records;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    { 
        $validatedData = $request->validate([
            'value' => 'required|numeric',
        ]);

        //check if user has exceeded the maximux records count
        $check = Record::where('user_id',Auth::user()->id)->count();
        if($check >= 5) {
            return response()->json(['error' => 'user has exceeded the maximun number of allowd records'],403);
        }
        //create a record 
        $record_id = $this->getRecordNumber();
        $record = new Record;
        $record->id = $record_id;
        $record->user_id =  Auth::user()->id ;
        $record->value = $request->value ;
        $record->freeze = 0;
        $record->save();
        //create history instance
        $history = new History;
        $history->record_id = $record_id;
        $history->operation = "create";
        $history->op_value = $request->value ;
        $history->value = $request->value ;
        $history->steps = 0;
        $history->save() ;

        //return submitted data
        $success['step']= 0;
        $success['operation']= 'create';
        $success['value']= $request->value;
        $success['id']=$record_id ;
        return response()->json(['success' => $success],200);

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        //show record
        $record = $this->checkRecordId($id);
        if($record == null) return response()->json(['error' => 'invalid record id'],403);  

        $reference = Fork::where('forking_id',$record->id)->first();

        $success['value'] = $record->value ;
        $success['freeze'] = $record->freeze ;
        if($reference != null) $success['reference'] = $reference->forked_id ;
        return response()->json(['success' => $success],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'value' => 'required|numeric',
            'operation' => 'required|string',
        ]);

        //check record id
        $record = $this->checkRecordId($id);
        if($record == null) return response()->json(['error' => 'invalid record id'],403);     
        if($record->freeze == 1) return response()->json(['error' => 'the record is freezed'],403); 
        //perfom operations
        $result = $record->value ;
        switch ($request->operation){
            case 'add': $result +=$request->value; break ;
            case 'sub': $result -=$request->value; break ;
            case 'mul': $result *=$request->value; break ;
            case 'div': {
                if($result ==0) return response()->json(['error' => 'current record value is 0, you can not divide by 0 '],403);
                $result /=$request->value; 
                break ;
            }
            default: return response()->json(['error' => 'invalid operation'],403);
        }

        //update record value
        $newRecord = Record::where('id',$record->id)
            ->update(['value' => $result]);
        // add to history
        $step = History::where('record_id',$record->id)
            ->orderBy('steps', 'desc')
            ->first();

        $history = new History;
        $history->record_id = $record->id;
        $history->operation = $request->operation;
        $history->op_value = $request->value ;
        $history->value = $result ;
        $history->steps = $step->steps + 1 ;
        $history->save() ;

        $success['step'] = $step->steps + 1;
        $success['value'] = $result; 
        $success['operation'] = $request->operation; 
        return response()->json(['success' => $success],200);
        
    }

    public function viewHistory($id){
        //check record id
        $record = $this->checkRecordId($id);
        if($record == null) return response()->json(['error' => 'invalid record id'],403);
        //fetch history data
        $history = History::select('steps','operation','op_value','value','created_at')
            ->where('record_id', $record->id)
            ->orderBy('steps', 'asc')
            ->get();
        return $history;
        
    }

    public function freeze ($id) {
        //check record id
        $record = $this->checkRecordId($id);
        if($record == null) return response()->json(['error' => 'invalid record id'],403);

        //freeze
        $newRecord = Record::where('id',$record->id)
            ->update(['freeze' => 1]);
        $success['id'] = $record->id;
        $success['status'] = 'freezed';  
        return response()->json(['success' => $success],200);
    }

    public function unfreeze ($id) {
        //check record id
        $record = $this->checkRecordId($id);
        if($record == null) return response()->json(['error' => 'invalid record id'],403);

        //unfreeze
        $newRecord = Record::where('id',$record->id)
            ->update(['freeze' => 0]);
        $success['id'] = $record->id;
        $success['status'] = 'unfreezed';  
        return response()->json(['success' => $success],200);       
    }

    public function operationsCount ($id) {
        //check record id
        $record = $this->checkRecordId($id);
        if($record == null) return response()->json(['error' => 'invalid record id'],403);

        $operations = History::where('record_id',$record->id)
            ->count();
        $success['id'] = $record->id;
        $success['op_count'] = $operations;
        return response()->json(['success' => $success],200);
    }

    public function totalOperationsCount(){
        $records = Record::where('user_id',Auth::user()->id)
            ->join('history', 'records.id', '=', 'history.record_id')
            ->count();
        
        $success['op_count'] = $records;
        return response()->json(['success' => $success],200);   
    }

    public function fork ($id) {
        //check record id
        $record = $this->checkRecordId($id);
        if($record == null) return response()->json(['error' => 'invalid record id'],403);

        //create new record 
        $newRecord_id = $this->getRecordNumber();
        $newRecord = new Record;
        $newRecord->id = $newRecord_id;
        $newRecord->user_id =  Auth::user()->id ;
        $newRecord->value = $record->value ;
        $newRecord->freeze =  $record->freeze;
        $newRecord->save();
        //create fork instance
        $fork = new Fork;
        $fork->forked_id =  $record->id;
        $fork->forking_id =  $newRecord_id;
        $fork->save();
        //create history
        $oldHistory = History::where('record_id',$record->id)->get();
        foreach ($oldHistory as $history){
            $newHistory = new History;
            $newHistory->record_id = $newRecord_id;
            $newHistory->operation = $history->operation;
            $newHistory->op_value = $history->op_value ;
            $newHistory->value = $history->value ;
            $newHistory->steps = $history->steps;
            $newHistory->save() ;
        }

        //return submitted data
        $success['id']= $newRecord_id;
        $success['value']= $newRecord->value;
        $success['reference']=  $record->id;
        return response()->json(['success' => $success],200);


    }

    public function rollback ($id){
        //check record id
        $record = $this->checkRecordId($id);
        if($record == null) return response()->json(['error' => 'invalid record id'],403);

        $rollback = History::where('record_id',$record->id)
            ->orderBy('steps', 'desc')
            ->first()
            ->delete();
        if($rollback == 0) return response()->json(['error' => 'no history for this record'],403);

        $data = History::select('steps','operation','op_value','value','created_at')
            ->where('record_id',$record->id)
            ->orderBy('steps', 'desc')
            ->first();
        //update record value
        $newRecord = Record::where('id',$record->id)
        ->update(['value' => $data->value]);
        
        return response()->json(['success' =>  $data],200);   


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        //delete record
        $record = Record::where('user_id',Auth::user()->id)
            ->where('id',$id)
            ->delete();
        if($record==0) return response()->json(['error' => 'invalid record id'],403);
        //delete record history
        $history = History::where('record_id',$id)->delete();
        return response()->json(['success' => ''],200);
    }

    public function getRecordNumber(){
        do{
            $rand = $this->generateRandomId(5);
         }while(!empty(Record::where('id',$rand)->first()));
        return $rand;
    }

   public function generateRandomId($length) {
       $characters = '0123456789';
       $charactersLength = strlen($characters);
       $randomString = '';
       for ($i = 0; $i < $length; $i++) {
           $randomString .= $characters[rand(0, $charactersLength - 1)];
       }
       return $randomString;
    }

    public function checkRecordId($id){
        $record = Record::where('user_id',Auth::user()->id)
            ->where('id',$id)
            ->first();
        return $record;
    }

}
