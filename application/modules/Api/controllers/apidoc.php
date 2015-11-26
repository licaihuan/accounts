<?php  
    /**
     * @apiDefine myErrRet
     * @apiSuccess {Number} errno 错误码
     * @apiSuccess {String} msg  错误信息
     * @apiErrorExample {json} Error-Response:
     * {"errno":"错误码","msg":"错误码解释"}
     */
    
    /**
     * @apiDefine tokenSign
     * @apiParam {String} token 会话token
     * @apiParam {Number} timestamp 时间戳
     * @apiParam {String{32}} sign 签名(会话签名)
     */

    /**
     * @apiDefine systemSign
     * @apiParam {String} app_id 系统分配的接入编号
     * @apiParam {String} timestamp 时间戳
     * @apiParam {String{32}} sign 系统签名
     */
    
    /**
     * @apiDefine mySuccArr
     * @apiSuccess {Number} errno 错误码
     * @apiSuccess {String} msg  错误信息
     * @apiSuccess {-} data 结果信息数据
     * @apiSuccessExample {json} Succ-Response:
     * {"errno":"0","msg":"succ","data":{}}
     */
    
    /**
     * @apiDefine mySuccList
     * @apiSuccess {Number} errno 错误码
     * @apiSuccess {String} msg  错误信息
     * @apiSuccess {-} data 结果信息数据
     * 
     * @apiSuccessExample {json} Succ-Response:
     * {"errno":"0","msg":"succ","data":[]}
     */
    
?>
