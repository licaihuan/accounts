<?php
class AccountsSvc
{/*{{{*/
	const OBJ = 'Accounts';
	private function add($param)
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

	private function releaseAccountsLock($accountid)
	{
		$lock = 'ACCOUNTS_'.$accountid;
		$r = MysqlSvc::releaseLock($lock);
		return $r;
	}

	private function getAccountsLock($accountid)
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
	static public getAccountsInfo($uid,$cat = Accounts::CAT_CASH)
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
					$obj = FreezesSvc::getById($freezesid)
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
	 *	'orderid'=> '', //订单号(可选)
	 *	'out_trans_id'=>'',//第三方交易单号
     *
	 */
	static public function accountingProcess($response,$accountid,$transid,$cat,$from,$remark = '')
	{
		$ret = array(
			'e'=>ErrorSvc::ERR_OK,
		);

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

							$_amount_ = $response['amount'];
							$_fee_ = $response['fee'];
							if($transobj->type = Transaction::TYPE_IN){
								$_type = Accountingrecord::TYPE_IN;
								if($transobj->$tin != $_amount_){
									$ret = array(
										'e'=>ErrorSvc::ERR_RESPONSE_NOT_MACHED,
									);

									$f_log = array_merge($response,$ret);
									LogSvc::fileLog('ERROR_AccountsSvc.accountingProcess',$_f_log);
									LoaderSvc::loadExecutor()->rollback();
									self::releaseAccountsLock($accountid);
									return $ret;
								}

								$amount = $transobj->$tin - $transobj->$fee;
								$balance = $accountobj->balance + $amount;
							}else{
								$_type = Accountingrecord::TYPE_OUT;
								if($transobj->$tout != $_amount_){
									$ret = array(
										'e'=>ErrorSvc::ERR_RESPONSE_NOT_MACHED,
									);
									
									$f_log = array_merge($response,$ret);
									LogSvc::fileLog('ERROR_AccountsSvc.accountingProcess',$_f_log);
									LoaderSvc::loadExecutor()->rollback();
									self::releaseAccountsLock($accountid);
									return $ret;
								}
								$amount = $transobj->$tout + $transobj->$fee;
								$balance = $accountobj->balance - $amount;
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

							if($_type == Accountingrecord::TYPE_IN){
								$log['in'] = $_amount_;
							}else{
								$log['out'] = $_amount_;
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
		$r = self::updateById($accountid,$params);
		return $r;
	}




}

