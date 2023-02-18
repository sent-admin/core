<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------


declare(strict_types=1);

namespace Sent\Support;

class Tree{

	protected $formatTree;

	/**
	 * 把返回的数据集转换成Tree
	 * @param array $list 要转换的数据集
	 * @param string $pid parent标记字段
	 * @param string $level level标记字段
	 * @return array
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function listToTree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
		// 创建Tree
		$tree = array();
		if (is_array($list)) {
			// 创建基于主键的数组引用
			$refer = array();
			foreach ($list as $key => $data) {
				$refer[$data[$pk]] = &$list[$key];
			}
			foreach ($list as $key => $data) {
				// 判断是否存在parent
				$parentId = $data[$pid];
				if ($root == $parentId) {
					$tree[] = &$list[$key];
				} else {
					if (isset($refer[$parentId])) {
						$parent             = &$refer[$parentId];
						$parent['childs'][] = $data['id'];
						$parent[$child][]   = &$list[$key];
					}
				}
			}
		}
		return $tree;
	}

	/**
	 * 获得所有的子
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getChilds($data, $id = 0, $pk = 'id', $pid = 'pid') {

		$array = [];
		foreach ($data as $k => $v) {
			if ($v[$pid] == $id) {
				$array[] = $v[$pk];
				array_merge($array, $this->getChilds($data, $v[$pk]));
			}
		}
		return $array;
	}

	/**
	 * 获取id的所有父，包含自己
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getParents($data, $id = 0, $pk = 'id', $pid = 'pid') {
		static $ids = [];
		foreach ($data as $k => $v) {
			if ($v[$pk] == $id) {
				array_unshift($ids, $id);
				if ($v['pid'] == 0) {
					break;
				}
				$this->getParents($data, $v[$pid]);
			}
		}
		return $ids;
	}

	/**
	 * 将树子节点加层级成列表
	 * @param  [type]  $tree  [description]
	 * @param  integer $level [description]
	 * @return [type]         [description]
	 */
	protected function _toFormatTree($tree, $level = 1) {
		foreach ($tree as $key => $value) {
			$temp = $value;
			if (isset($temp['_child'])) {
				$temp['_child'] = true;
				$temp['level']  = $level;
			} else {
				$temp['_child'] = false;
				$temp['level']  = $level;
			}
			array_push($this->formatTree, $temp);
			if (isset($value['_child'])) {
				$this->_toFormatTree($value['_child'], ($level + 1));
			}
		}
	}

	protected function catEmptyDeal($cat, $next_parentid, $pid = 'pid', $empty = "&nbsp;&nbsp;&nbsp;&nbsp;") {
		$str = "";
		if ($cat[$pid]) {
			for ($i = 2; $i < $cat['level']; $i++) {
				$str .= $empty . "│";
			}
			if ($cat[$pid] != $next_parentid && !$cat['_child']) {
				$str .= $empty . "└─&nbsp;";
			} else {
				$str .= $empty . "├─&nbsp;";
			}
		}
		return $str;
	}

	/**
	 * 格式化树
	 * @param  [type]  $list  [description]
	 * @param  string  $title [description]
	 * @param  string  $pk    [description]
	 * @param  string  $pid   [description]
	 * @param  integer $root  [description]
	 * @return [type]         [description]
	 */
	public function toFormatTree($list, $title = 'title', $pk = 'id', $pid = 'pid', $root = 0) {
		if (empty($list)) {
			return false;
		}
		$list             = $this->listToTree($list, $pk, $pid, '_child', $root);
		$this->formatTree = array();
		$this->_toFormatTree($list);
		$data = array();
		foreach ($this->formatTree as $key => $value) {
			$index               = ($key + 1);
			$next_parentid       = isset($this->formatTree[$index][$pid]) ? $this->formatTree[$index][$pid] : '';
			$value['level_show'] = $this->catEmptyDeal($value, $next_parentid);
			$value['title_show'] = $value['level_show'] . $value[$title];
			$data[]              = $value;
		}
		return $data;
	}
}