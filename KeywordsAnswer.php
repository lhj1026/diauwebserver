<?php

include_once "wx_config.php";
include_once 'apiUpdateRule.php';
class KeywordsAnswer
{
    public $MatchKeyword;
    public $writeMatchedQuestion;


    public function KeywordsAnswer()
    {
        $MatchKeyword=false;
        $writeMatchedQuestion=false;

    }
    public function readKeywordsAnswer()
    {
        //re-declare the variant when using external variant
        global $Rule_json;
        //if the rule is empty, then read from local file, or load from wechat platform
        if (($Rule_json==="")or empty($Rule_json)){
            $jsonKeywords=file_get_contents(Wx_Config::RULEFILE);
            if(!$jsonKeywords) {
                updateRuleDatabase();
                $jsonKeywords=file_get_contents(Wx_Config::RULEFILE);
            }

            $Rule_json=json_decode($jsonKeywords);
        }
    }
    public function giveAnswer($question)
    {
        global $Rule_json;

        $highestVal=0;
        $answer="";
        $defaultAnswer=$Rule_json->is_autoreply_open;

        $ruleItems=$Rule_json->keyword_autoreply_info->list;
        $RuleNum=count($ruleItems);
        for($index =0;$index<$RuleNum;$index++)
        {
            $item=$ruleItems[$index];
            $eval=$this->evalAnswer($question,$item->keyword_list_info);
            if($eval>$highestVal){
                $highestVal=$eval;
                $answer=$item->reply_list_info[0];
            }
        }
        //if did not find
        if($highestVal===0){
            $this->MatchKeyword=false;
            if($defaultAnswer===1) {
                $defaultMsg=$Rule_json->message_default_autoreply_info;
                return $defaultMsg;
            }else{
                return "";
            }
        }else{
            $this->MatchKeyword=true;
            return $answer;
        }

    }
    private function evalAnswer($question,$keyWords)
    {
        $evalResult=0;
        for($index=0;$index<count($keyWords);$index++){
            $item=$keyWords[$index];
            //echo "\nkeywords $index";
            //var_dump($item);
            if($item->match_mode==="contain") {
                if (strpos($question, $item->content) !== false) {
                    $evalResult++;
                }
            }else{
                if($item->match_mode==="equal") {
                    if ($question === $item->content) {
                        $evalResult+=1.5;
                    }
                }
            }

        }
        return $evalResult;
    }



}