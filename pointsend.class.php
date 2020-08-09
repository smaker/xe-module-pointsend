<?php
/**
 * @class pointsend
 * @author 퍼니XE (funnyxe@simplesoft.io)
 * @brief  pointsend 모듈의 high class
 **/

class pointsend extends ModuleObject
{

	/**
	 * @brief 설치시 추가 작업이 필요할시 구현
	 **/
	public function moduleInstall()
	{
		// 2010.03.04 회원 탈퇴 시 모든 정보를 삭제하는 트리거 추가
		getController('module')->insertTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after');

		return $this->makeObject();
	}

	/**
	 * @brief 설치가 이상이 없는지 체크하는 method
	 **/
	public function checkUpdate()
	{
		$oDB = DB::getInstance();
		$oModuleModel = getModel('module');

		if(!$oDB->isColumnExists('pointsend_log', 'comment'))
		{
			return true;
		}

		// 수수료 column 추가
		if(!$oDB->isColumnExists('pointsend_log', 'fee'))
		{
			return true;
		}

		if(!getModel('module')->getTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after')) 
		{
			return true;
		}

		return false;
	}

	/**
	 * 업데이트 실행
	 **/
	function moduleUpdate()
	{
		$oDB = DB::getInstance();
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');

		if(!$oDB->isColumnExists('pointsend_log', 'comment'))
		{
			$oDB->addColumn('pointsend_log','comment','text','',0);
		}

		// 수수료 column 추가
		if(!$oDB->isColumnExists('pointsend_log', 'fee'))
		{
			$oDB->addColumn('pointsend_log', 'fee', 'number', 11, 0);
			$oDB->addIndex('pointsend_log', 'idx_fee', 'fee');
		}

		if(!getModel('module')->getTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after')) 
		{
			getController('module')->insertTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after');
		}


		return $this->makeObject(0,'success_updated');
	}

	/**
	 * 캐시 파일 재생성
	 **/
	public function recompileCache()
	{
	}

	/**
	 * @brief 쪽지 내용에 포함된 치환자를 정리
	 */
	public function arrangeMessageContent($sender_info, $receiver_info, $point, &$content)
	{
		$content = str_replace('%_SENDER_%', sprintf('<span class="member_%s">%s</span>', $sender_info->member_srl ,$sender_info->nick_name), $content);
		$content = str_replace('%_RECEIVER_%', sprintf('<span class="member_%s">%s</span>', $receiver_info->member_srl ,$receiver_info->nick_name), $content);
		$content = str_replace('%SENDER%', $sender_info->nick_name, $content);
		$content = str_replace('%SENDER_ID%', $sender_info->user_id, $content);
		$content = str_replace('%SENDER_SRL%', $sender_info->member_srl, $content);
		$content = str_replace('%RECEIVER%', $receiver_info->nick_name, $content);
		$content = str_replace('%RECEIVER_ID', $receiver_info->user_id, $content);
		$content = str_replace('%RECEIVER_SRL', $receiver_info->member_srl, $content);
		$content = str_replace('%POINT%', $point, $content);
	}
	
	public function makeObject($code = 0, $msg = 'success')
	{
		return class_exists('BaseObject') ? new BaseObject($code, $msg) : new Object($code, $msg);
	}
}

/* End of file : pointsend.admin.view.php */
/* Location : ./modules/pointsend/pointsend.admin.view.php */