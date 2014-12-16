<?php
/**
 * TOP API: taobao.shop.user.get request
 * 
 * @author auto create
 * @since 1.0, 2012-12-07 16:40:13
 */
class ShopUserGetRequest
{
	/** 
	 * 待查询指标：UserAction数据结构的公开信息字段列表，以半角逗号（,）分割
	 **/
	private $fields;
	
	/** 
	 * 统计时间窗口：其中
“30D”=为最近30天
“12M“=为最近12月
	 **/
	private $scope;
	
	/** 
	 * 用户昵称
	 **/
	private $userNick;
	
	private $apiParas = array();
	
	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setScope($scope)
	{
		$this->scope = $scope;
		$this->apiParas["scope"] = $scope;
	}

	public function getScope()
	{
		return $this->scope;
	}

	public function setUserNick($userNick)
	{
		$this->userNick = $userNick;
		$this->apiParas["user_nick"] = $userNick;
	}

	public function getUserNick()
	{
		return $this->userNick;
	}

	public function getApiMethodName()
	{
		return "taobao.shop.user.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->fields,"fields");
		RequestCheckUtil::checkNotNull($this->scope,"scope");
		RequestCheckUtil::checkNotNull($this->userNick,"userNick");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
