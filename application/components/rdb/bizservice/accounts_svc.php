<?php
class AccountsSvc
{/*{{{*/
	const OBJ = 'Accounts';
	private static function add($param)
	{
		$obj = Accounts::createByBiz($param);
		return self::getDao()->add($obj);
	}

	private function getCreateAccountsLock($uid,$cat)
	{
		$lock = 'CREATE_ACCOUNTS_'.$uid.'_'.$cat;
		$r = MysqlSvc::getLock($lock);
		return $r;
	}

	private function releaseCreateAccountsLock($uid,$cat)
	{
		$lock = 'CREATE_ACCOUNTS_'.$uid.'_'.$cat;
		$r = MysqlSvc::releaseLock($lock);
		return $r;
	}

	private static function releaseAccountsLock($accountid)
	{
		$lock = 'ACCOUNTS_'.$accountid;
		$r = MysqlSvc::releaseLock($lock);
		return $r;
	}

	private static function getAccountsLock($accountid)
	{
		$lock = 'ACCOUNTS_'.$accountid;
		$r = MysqlSvc::getLock($lock);
		return $r;
	}

	static public function createAccounts($uid,$cat)
	{
		$ret = array(
			'e'=>ErrorSvc::ERR_OK,
		);

		$r = self::getCreateAccountsLock($uid,$cat);
		if($r){
			LoaderSvc::loadExecutor()->beginTrans();
			$result = self::getByUidAndCat($uid,$cat);
			if(!empty($result)){
				$ret = array(
					'e'=>ErrorSvc::ERR_ACCOUNTS_EXISTS,
				);
				LoaderSvc::loadExecutor()->rollback();
			}else{
				$params = array(
					'uid'=>$uid,
					'cat'=>$cat,
				);
				$obj = self::add($params);
				if(is_object($obj) && LoaderSvc::loadExecutor()->inTrans()){
					LoaderSvc::loadExecutor()->commit();
				}else{
					LoaderSvc::loadExecutor()->rollback();
					$ret = array(
						'e'=>ErrorSvc::ERR_SYSTEM_ERROR,
					);
				}
			}
		}else{
			$ret = array(
				'e'=>ErrorSvc::ERR_MYSQL_GET_LOCK,
			);
		}
		self::releaseCreateAccountsLock($uid,$cat);
		return $ret;
	}

	static private function getById($id = '0')
	{
		if (empty($id))
		{
			return null;
		}
		return self::getDao()->getById($id,self::OBJ);
	}

	static private function updateById($id,$param)
	{
		return self::getDao()->updateById($id,$param,self::OBJ);
	}

	static private function getDao()
	{
		return LoaderSvc::loadDao(self::OBJ);
	}

    /**
	 * @brief 账户列表
	 */
	static public function lists($request = array(),$options = array(),$export = false)
	{/*{{{*/
		$request_param = array();
		$sql_condition = array();
		$sql_param = array();

		if(isset($request['id']) && $request['id']>1)
		{
			$request_param[] = '`id`=' . $request['id'];
			$sql_condition[] = '`id` = ? ';
			$sql_param[] = $request['id'];
		}
		if('' != $request['uid']){
			$request_param[] = 'uid=' . $request['uid'];
			$sql_condition[] = '`uid` = ?';
			$sql_param[]	 = $request['uid'];
		}

		$option = array();
		$option['len'] = ($options['len'] > 0) ? $options['len'] : PER_PAGE;
		if($options['page'] > 0){
			$option['offset'] = ($options['page'] - 1) * $option['len'];
		}
		$option['orderby'] = isset($options['orderby']) ? $options['orderby'] : '';

		$results = self::getDao()->getRecord($sql_condition,$sql_param ,$option);
		if($export) return $results;
		
		$pages = '';
		$total = $results['total'];
		if($total > 0){
			$temp = stristr($options['baseurl'],'?');
			if($temp === false) $options['baseurl'] .= '?';
			$options['baseurl'] .= implode('&',$request_param);
			if(count($request_param)) $options['baseurl'] .= '&';
			$pages = Pager::getPageStr($options['page'],$option['len'],$total,$options['baseurl']);
		}
		$results['pages'] = $pages;
		//$results['offset'] = $option['offset'] + 1;
		//$results['len'] = $option['len'];
		//$results['pagenums'] = ceil($total / $option['len']);

		return $results;
	}/*}}}*/
	
	/**
	 * @brief 获取用户指定账户
	 */
	static public function getByUidAndCat($uid,$cat = Accounts::CAT_CASH)
	{
		return self::getDao()->getByUidAndCat($uid,$cat);
	}

    /**
	 * @brief 获取账户信息
	 */
	static public function getAccountsInfo($uid,$cat = Accounts::CAT_CASH)
	{
		$r = self::getDao()->getByUidAndCat($uid,$cat);
		//账户不存在创建账户
		if(empty($r)) {
			self::createAccounts($uid,$cat);
			$r = self::getDao()->getByUidAndCat($uid,$cat);
		}

		$freezes = FreezesSvc::getFreezesSum($r['id']);
		$total = $r['balance'] + $freezes;
		$accountinfo = array(
			'balance'=>$r['balance'],
			'feezes'=>$freezes,
			'total'=>$total,
		);
		return $accountinfo;
	}

    /**
	 * @brief 锁定账户
	 */
	static private function lockAccountsRecord($accountid)
	{
		return self::getDao()->lockAccountsRecord($accountid);
	}
	
	/**
	 * @brief 内部转账
	 */
	static public function transfers($from,$to,$amount,$transid = '')
	{
		$ret = array(
			'e'=>ERR_OK,
		);
		$r1 = self::getAccountsLock($from);
		if(!$r1){
			$ret['e'] = ErrorSvc::ERR_MYSQL_GET_LOCK;
			return $ret;
		}
		$r2 = self::getAccountsLock($to);
		if(!$r2){
			self::releaseAccountsLock($from);
			$ret['e'] = ErrorSvc::ERR_MYSQL_GET_LOCK;
			return $ret;
		}
		
		$result1 = self::lockAccountsRecord($from);
		if(empty($result1)){
			$ret = array(
				'e'=>ErrorSvc::ERR_ACCOUNTS_NOT_FOUND,
			);
			self::releaseAccountsLock($from);
			self::releaseAccountsLock($to);
			return $ret;
		}
		
		$result2 = self::lockAccountsRecord($to);
		if(empty($result2)){
			$ret = array(
				'e'=>ErrorSvc::ERR_ACCOUNTS_NOT_FOUND,
			);
			self::releaseAccountsLock($from);
			self::releaseAccountsLock($to);
			return $ret;
		}
		
		LoaderSvc::loadExecutor()->beginTrans();
		
		$obj1 = self::getById($from);
		$balance1 = $obj1->balance - $amount;
		if($balance1 < 0){
			$ret['e'] = ErrorSvc::ERR_ACCOUNTS_BALANCE_SHORTAGE;
			LoaderSvc::loadExecutor()->rollback();
			return $ret;
		}
		$obj2 = self::getById($to);
		$balance2 = $obj2->balance + $amount;
		if(is_object($obj1) && is_object($obj2) && LoaderSvc::loadExecutor()->inTrans()){
			$r3 = self::updateBalance($from,$balance1);
			$r4 = self::updateBalance($to,$balance2);
			if($r3 && $r4){
				$transid = !empty($transid) ? $transid : SnSvc::createSerialNum(SnSvc::CHANNEL_ID_LOCAL,SnSvc::MODULE_ID_TRANSFERS);
				//写入账务流水
				$log1 = array(
					'datetime'=>date('Y-m-d H:i:s'),
					'accountid'=>$from,
					'remark'=>'转账',
					'cat'=>Accountingrecord::CAT_TRANSFERS_OUT,
					'from'=>Accountingrecord::FROM_SYS,
					'transid'=>$transid,
					'uid'=>$obj1->uid,
				    'out'=>$amount,
					'balance'=>$balance1,
					'type'=>Accountingrecord::TYPE_OUT,
					'state'=>Accountingrecord::STATE_NORMAL,
				);
				$log2 = array(
					'datetime'=>date('Y-m-d H:i:s'),
					'accountid'=>$to,
					'remark'=>'转账',
					'cat'=>Accountingrecord::CAT_TRANSFERS_IN,
					'from'=>Accountingrecord::FROM_SYS,
					'transid'=>$transid,
					'uid'=>$obj2->uid,
					'in'=>$amount,
					'balance'=>$balance2,
					'type'=>Accountingrecord::TYPE_IN,
					'state'=>Accountingrecord::STATE_NORMAL,
				);
				$r5 = AccountingrecordSvc::add($log1);
				$r6 = AccountingrecordSvc::add($log2);
				if($r5 && $r6){
					LoaderSvc::loadExecutor()->commit();
					return $ret;
				}				
			}
		}
		LoaderSvc::loadExecutor()->rollback();
		LogSvc::fileLog(__CLASS_.'_'.__FUNCTION__,'[转账失败:'."账户A({$from}）->账户B({$to})=>金额({$amount})".']');
		return array('e'=>ErrorSvc::ERR_ACCOUNTING_PROCESS_FAIL);
		
	}

    /**
	 * @brief 资金解冻
	 */
	static public function unfreeze($freezesid)
	{
		$ret = array(
			'e'=>ErrorSvc::ERR_OK,
		);
		$obj = FreezesSvc::getById($freezesid);
		if(!is_object($obj)){
			$ret = array(
				'e'=>ErrorSvc::ERR_UNFREEZE_FAIL,
			);
			return $ret;
		}

		$accountid = $obj->accountid;
		$r = self::getAccountsLock($accountid);
		if(!$r){
			$ret = array(
				'e'=>ErrorSvc::ERR_MYSQL_GET_LOCK,
			);
		}else{
			LoaderSvc::loadExecutor()->beginTrans();
			//锁定记录
			$result = self::lockAccountsRecord($accountid);
			if(empty($result)){
				$ret = array(
					'e'=>ErrorSvc::ERR_ACCOUNTS_NOT_FOUND,
				);
				LoaderSvc::loadExecutor()->rollback();
			}else{
				if(LoaderSvc::loadExecutor()->inTrans()){
					$obj = FreezesSvc::getById($freezesid);
					if($obj->state != Freezes::STATE_FREEZE_IN){
						$ret = array(
							'e'=>ErrorSvc::ERR_UNFREEZE_FAIL,
						);
						LoaderSvc::loadExecutor()->rollback();
					}else{
						$accountobj = self::getById($accountid);
						$balance = $accountobj->balance + $obj->amount;
						//更新账户余额
						$r1 = self::updateBalance($accountid,$balance);

						//更新冻结状态
						$r2 = FreezesSvc::updateById($freezesid,array('state'=>Freezes::STATE_DEFROSTED));
						if($r1 && $r2){
							LoaderSvc::loadExecutor()->commit();
						}else{
							$ret = array(
								'e'=>ErrorSvc::ERR_SYSTEM_ERROR,
							);
							LoaderSvc::loadExecutor()->rollback();
						}

					}
					
				}else{
					$ret = array(
						'e'=>ErrorSvc::ERR_SYSTEM_ERROR,
					);
				}
			}
		}
		
		self::releaseAccountsLock($accountid);
		return $ret;

	}


	/**
	 * @brief 资金冻结
	 */
	static public function freezes($accountid,$amount,$cat,$remark = '',$transid = '')
	{
		$ret = array(
			'e'=>ErrorSvc::ERR_OK,
		);
		
		$amount = sprintf("%.2f",$amount);
		//锁定账户
		$r = self::getAccountsLock($accountid);
		if(!$r){
			$ret = array(
				'e'=>ErrorSvc::ERR_MYSQL_GET_LOCK,
			);
		}else{
			LoaderSvc::loadExecutor()->beginTrans();
			//锁定记录
			$result = self::lockAccountsRecord($accountid);
			if(empty($result)){
				$ret = array(
					'e'=>ErrorSvc::ERR_ACCOUNTS_NOT_FOUND,
				);
				LoaderSvc::loadExecutor()->rollback();
			}else{
				if(LoaderSvc::loadExecutor()->inTrans()){
					$accountobj = self::getById($accountid);
					$balance = $accountobj->balance;
					if($balance < $amount){
						$ret = array(
							'e'=>ErrorSvc::ERR_ACCOUNTS_BALANCE_SHORTAGE,
						);
						LoaderSvc::loadExecutor()->rollback();
					}else{
						$balance = $balance - $amount;

						//更新账户余额
						$r1 = self::updateBalance($accountid,$balance);

						//写入冻结记录
						$freezesparams = array(
							'accountid'=>$accountid,
							'transid'=>(!empty($transid) ? $transid : SnSvc::createSerialNum(SnSvc::CHANNEL_ID_LOCAL)),
							'amount'=>$amount,
							'remark'=>$remark,
							'cat'=>$cat,
							'state'=>Freezes::STATE_FREEZE_IN,
						);
						
						$r2 = FreezesSvc::add($freezesparams);
						if($r1 && is_object($r2)){
							LoaderSvc::loadExecutor()->commit();
						}else{
							$ret = array(
								'e'=>ErrorSvc::ERR_SYSTEM_ERROR,
							);
							LoaderSvc::loadExecutor()->rollback();
						}

					}
				}else{
					$ret = array(
						'e'=>ErrorSvc::ERR_SYSTEM_ERROR,
					);
				}

			}

		}

		self::releaseAccountsLock($accountid);
		return $ret;

	}

	/**
     *
	 * @brief 账务处理
	 *
	 *
	 *  $response 说明：
	 *	'amount'=>1.00, //交易金额(不包含手续费)
	 *  'fee'=>0.00，//手续费
	 *	
	 *
	 *	 $accountid   账户ID      必选
	 *	 $transid     交易ID      必选
	 *	 $cat         业务类型     必选
	 *   $from        一级渠道     必选
	 *   $remark      备注        可选
	 *   channelid    子渠道      必选
	 *   tradeno      第三方交易号 必选
     *
	 */
	static public function accountingProcess($response = array(),$accountid,$transid,$cat,$from,$remark = '')
	{
		$ret = array(
			'e'=>ErrorSvc::ERR_OK,
		);
		
		$channelid = $response['channelid'];
		$tradeno = $response['tradeno'];
		$_amount_ = sprintf("%.2f",$response['amount']);
		$_fee_ = sprintf("%.2f",$response['fee']);
		
		if($_amount_ < 0 || $_fee_ < 0){
			$ret = array(
				'e'=>ErrorSvc::ERR_PARAM_INVALID,
			);
			return $ret;
		}

		//锁定账户
		$r = self::getAccountsLock($accountid);
		if(!$r){
			$ret = array(
				'e'=>ErrorSvc::ERR_MYSQL_GET_LOCK,
			);
		}else{
			//开启事务处理
			LoaderSvc::loadExecutor()->beginTrans();
			//锁定记录
			$result = self::lockAccountsRecord($accountid);
			if(empty($result)){
				$ret = array(
					'e'=>ErrorSvc::ERR_ACCOUNTS_NOT_FOUND,
				);
				LoaderSvc::loadExecutor()->rollback();
			}else{
				$transobj = TransactionSvc::getById($transid);
				if(!is_object($transobj)){
					$ret = array(
						'e'=>ErrorSvc::ERR_TRANSACTION_NOT_FOUND,
					);
					LoaderSvc::loadExecutor()->rollback();
				}else{
					if($transobj->state == Transaction::STATE_SUCC){
						$ret = array(
							'e'=>ErrorSvc::ERR_TRANSACTION_RESPONSE_REPEAT,
						);
						LoaderSvc::loadExecutor()->rollback();
					}else{
						if(LoaderSvc::loadExecutor()->inTrans()){
							$accountobj = self::getById($accountid);
							if($transobj->type == Transaction::TYPE_IN){
								$_type_ = Accountingrecord::TYPE_IN;
								if(bccomp($transobj->tin,$_amount_,2) != 0){
									$ret = array(
										'e'=>ErrorSvc::ERR_RESPONSE_NOT_MACHED,
									);

									$_f_log_ = array_merge(
										$response,
										array(
											'transid'=>$transid,
											'from'=>$from,
											'cat'=>$cat,
											'remark'=>$remark,
										),
										$ret
									);
									LogSvc::fileLog('ERROR_'.__CLASS__.'.'.__FUNCTION__,$_f_log_);
									LoaderSvc::loadExecutor()->rollback();
									self::releaseAccountsLock($accountid);
									return $ret;
								}

								$amount = bcsub($_amount_,$_fee_,2);
								$balance = bcadd($accountobj->balance,$amount,2);
							}else{
								$_type_ = Accountingrecord::TYPE_OUT;
								if(bccomp($transobj->tout,$_amount_,2) != 0){
									$ret = array(
										'e'=>ErrorSvc::ERR_RESPONSE_NOT_MACHED,
									);
									
									$_f_log_ = array_merge(
										$response,
										array(
											'transid'=>$transid,
											'from'=>$from,
											'cat'=>$cat,
											'remark'=>$remark,
										),
										$ret
									);
									LogSvc::fileLog('ERROR_'.__CLASS__.'.'.__FUNCTION__,$_f_log_);
									LoaderSvc::loadExecutor()->rollback();
									self::releaseAccountsLock($accountid);
									return $ret;
								}
								$amount = bcadd($_amount_,$_fee_,2);
								$balance = bcsub($accountobj->balance,$_amount_,2);

								if($balance < 0){
									$ret = array(
										'e'=>ErrorSvc::ERR_ACCOUNTS_BALANCE_SHORTAGE,
									);
									LoaderSvc::loadExecutor()->rollback();
									self::releaseAccountsLock($accountid);
									return $ret;
								}

							}

							//更新交易状态
							$transparams = array(
								'state'=>Transaction::STATE_SUCC,
							    'channelid'=>$channelid,
								'tradeno'=>$tradeno,
								'fee'=>$_fee_,
							);

							$r1 = TransactionSvc::updateById($transid,$transparams);
							
							//更新账户余额
							$r2 = self::updateBalance($accountid,$balance);

							//写入账务流水
							$log = array(
								'datetime'=>date('Y-m-d H:i:s'),
								'accountid'=>$accountid,
								'remark'=>$remark,
								'cat'=>$cat,
								'fee'=>$_fee_,
								'from'=>$from,
								'transid'=>$transid,
								'uid'=>$transobj->uid,
								'balance'=>$balance,
								'state'=>Accountingrecord::STATE_NORMAL,
							);

							if($_type_ == Accountingrecord::TYPE_IN){
								$log['in'] = $_amount_;
								$log['type'] = Accountingrecord::TYPE_IN;
							}else{
								$log['out'] = $_amount_;
								$log['type'] = Accountingrecord::TYPE_OUT;
							}

							$r3 = AccountingrecordSvc::add($log);
							if($r1 && $r2 && is_object($r3)){
								LoaderSvc::loadExecutor()->commit();
							}else{
								$ret = array(
									'e'=>ErrorSvc::ERR_SYSTEM_ERROR,
								);
								LoaderSvc::loadExecutor()->rollback();
							}

						}else{
							$ret = array(
								'e'=>ErrorSvc::ERR_SYSTEM_ERROR,
							);
						}
					}
				}
			}
		}

		self::releaseAccountsLock($accountid);
		return $ret;
	}

	/**
	 * @brief 更新账户余额
	 */
	private static function updateBalance($accountid,$balance)
	{
		$params = array(
			'balance'=>$balance,
			'utime' =>date('Y-m-d H:i:s'),
		);

		//更新账户余额
		if(LoaderSvc::loadExecutor()->inTrans())
			$r = self::updateById($accountid,$params);
		else $r = 0;
		return $r;
	}




}

