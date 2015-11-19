<?php

/**
 * @brief 账户接口
 */
class AccountsController extends ApibaseController
{
    /** 
    * @brief 账户预览
    * 
    * @author liuweidong
    * @date 2015-11-14
    * @return 
    */ 
    public function PreviewAction()
    {/*{{{*/
        $uid = $this->uid;
        $ret = $this->initOutPut();
        $accountinfo = AccountsSvc::getAccountsInfo($uid);
        $ret['data'] = $accountinfo;
        $this->outPut($ret);
    }/*}}}*/

    /** 
    * @brief 账户预览
    * 
    * @author liuweidong
    * @date 2015-11-14
    * @return 
    */ 
    public function LisAction()
    {/*{{{*/   
    }/*}}}*/

}
