<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class IssueController extends Controller
{    
    /**
     * show
     *
     * @param  mixed $issue
     * @return void
     */
    public function show($issue)
    {
        
        $result = $this->getIssue($issue);

        if(!isset( $result['status'] ))
            return response()->json('404 not found',404);
        
        if( $result['status'] == 'voting' )
            $result = $this->hideVote($result);

        return response()->json($result,200);
    }
    
    /**
     * join
     *
     * @param  mixed $request
     * @param  mixed $issue
     * @return void
     */
    public function join(Request $request, $issue)
    {
        $name = \Auth::user()->name;
        
        $result = $this->getIssueOrCreate($issue);
        
        if($result['status'] == 'reveal')
            return response()->json('voting is over',403);

        $key = $this->searchForName($name, $result['members']);
        
        if(!is_numeric($key))
        {
            $this->addUser($issue,$name);

            return response()->json('user join ok',200);
        }

        return response()->json('the user is already joined',403);

    }
    
    /**
     * vote
     *
     * @param  mixed $request
     * @param  mixed $issue
     * @return void
     */
    public function vote(Request $request, $issue)
    {
        $result = $this->getIssue($issue);

        if(!isset( $result['status'] ))
            return response()->json('404 Not Found',404);

        if( $result['status'] == 'reveal')
            return response()->json('voting is over',403);
        
        $key = $this->searchForName(\Auth::user()->name, $result['members']);

        if(!is_numeric($key))
            return response()->json('the user is not attached to the issue',403);
        
        if( $result['members'][$key][ 'status' ] == 'voted' || $result['members'][$key][ 'status' ] == 'passed')
            return response()->json('The user has already voted or passed it',403);
        
        $vote = $request->input('vote');
        
        $this->toEmitVote($issue,$vote);

        return response()->json('vote cast correctly',201);

        
    }
    
    /**
     * getIssue
     *
     * @param  mixed $issue
     * @return void
     */
    public function getIssue($issue)
    {
        $issue = Redis::get('issue:'.$issue);

        return json_decode($issue,true);
    }
    
    /**
     * getIssueOrCreate
     *
     * @param  mixed $issue
     * @return void
     */
    public function getIssueOrCreate($issue)
    {
        $result = $this->getIssue($issue);

        if(!isset( $result['status'] ))
        {
            $result = [
                'status'=>"voting",
                'members'=>[],
            ];

            Redis::set('issue:'.$issue, json_encode( $result ));
        }

        return $result;
    }
    
    /**
     * addUser
     *
     * @param  mixed $issue
     * @param  mixed $name
     * @return void
     */
    public function addUser($issue,$name)
    {
        $result = $this->getIssue($issue);

        array_push($result['members'],[
            'name'=>$name,
            'status'=>'waiting'
        ]);
        
        Redis::set('issue:'.$issue, json_encode( $result ));

        return $result;

    }   


    public function toEmitVote($issue,$vote)
    {
        $result = $this->getIssue($issue);

        $name = \Auth::user()->name;

        $key = $this->searchForName($name, $result['members']);
        
        if( $vote > 0 )
        {
            $result['members'][$key]['value'] = $vote;
            $result['members'][$key]['status'] = 'voted';

        } else {
            $result['members'][$key]['status'] = 'passed';
        }
        
        Redis::set('issue:'.$issue, json_encode( $result ));

        return $result;

    }
    
    /**
     * searchForName
     *
     * @param  mixed $name
     * @param  mixed $array
     * @return void
     */
    public function searchForName($name, $array) 
    {
        foreach ($array as $key => $val) 
        {
            if ($val['name'] === $name) {
                
                return $key;
            
            }
        }
        return false;
    }
    
    /**
     * hideVote
     *
     * @param  mixed $issue
     * @return void
     */
    public function hideVote($issue)
    {
        foreach ($issue['members'] as $key => $member) 
        {
            unset($issue['members'][$key]['value']);
        }

        unset($issue['avg']);

        return $issue;
    }
}
