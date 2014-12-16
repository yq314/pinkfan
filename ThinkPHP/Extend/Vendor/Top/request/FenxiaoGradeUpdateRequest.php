<?php
/**
 * TOP API: taobao.fenxiao.grade.update request
 * 
 * @author auto create
 * @since 1.0, 2012-12-07 16:40:13
 */
class FenxiaoGradeUpdateRequest
{
	/** 
	 * 等级ID
	 **/
	private $gradeId;
	
	/** 
	 * 等级名称，等级名称不可重复
	 **/
	private $name;
	
	private $apiParas = array();
	
	public function setGradeId($gradeId)
	{
		$this->gradeId = $gradeId;
		$this->apiParas["grade_id"] = $gradeId;
	}

	public function getGradeId()
	{
		return $this->gradeId;
	}

	public function setName($name)
	{
		$this->name = $name;
		$this->apiParas["name"] = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getApiMethodName()
	{
		return "taobao.fenxiao.grade.update";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->gradeId,"gradeId");
		RequestCheckUtil::checkNotNull($this->name,"name");
		RequestCheckUtil::checkMaxLength($this->name,20,"name");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
