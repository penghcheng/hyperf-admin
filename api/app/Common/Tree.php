<?php
/**
 * Author: 凡墙<jihaoju@qq.com>
 * Time: 2017-8-7 16:40
 * Description:
 */

namespace App\Common;

/**
 * 树
 *
 * 0是根结点
 */
class Tree
{
    private $data = array();
    private $child = array(
        -1 => array()
    );
    private $layer = array(
        0 => 0
    );
    private $parent = array();
    private $value_field = '';

    private $idKeyName = 'id';
    private $valueKeyName = 'value';
    private $childrenKeyName = 'children';
    private $showChildrenKeyIfNull = true;

    /**
     * @return string
     */
    public function getIdKeyName()
    {
        return $this->idKeyName;
    }

    /**
     * @param string $idKeyName
     */
    public function setIdKeyName($idKeyName)
    {
        $this->idKeyName = $idKeyName;
    }

    /**
     * @return string
     */
    public function getValueKeyName()
    {
        return $this->valueKeyName;
    }

    /**
     * @param string $valueKeyName
     */
    public function setValueKeyName($valueKeyName)
    {
        $this->valueKeyName = $valueKeyName;
    }

    /**
     * @return string
     */
    public function getChildrenKeyName()
    {
        return $this->childrenKeyName;
    }

    /**
     * @param string $childrenKeyName
     */
    public function setChildrenKeyName($childrenKeyName)
    {
        $this->childrenKeyName = $childrenKeyName;
    }

    /**
     * @return bool
     */
    public function isShowChildrenKeyIfNull()
    {
        return $this->showChildrenKeyIfNull;
    }

    /**
     * @param bool $showChildrenKeyIfNull
     */
    public function setShowChildrenKeyIfNull($showChildrenKeyIfNull)
    {
        $this->showChildrenKeyIfNull = $showChildrenKeyIfNull;
    }

    /**
     * 构造函数
     *
     * @param mix $value
     */
    public function construct($value = 'root')
    {
        $this->setNode(0, -1, $value);
    }

    /**
     * 构造树
     *
     * @param array $nodes
     *        结点数组
     * @param string $id_field
     * @param string $parent_field
     * @param string $value_field
     */
    public function setTree($nodes, $id_field, $parent_field, $value_field)
    {
        $this->value_field = $value_field;
        foreach ($nodes as $node) {
            $this->setNode($node[$id_field], $node[$parent_field], $node);
        }
        $this->setLayer();
    }

    /**
     * 取得options
     *
     * @param int $layer
     * @param int $root
     * @param string $space
     * @return array (id=>value)
     */
    public function getOptions($layer = 0, $root = 0, $except = NULL, $space = '&nbsp;&nbsp;')
    {
        $options = array();
        $childs = $this->getChilds($root, $except);
        foreach ($childs as $id) {
            if ($id > 0 && ($layer <= 0 || $this->getLayer($id) <= $layer)) {
                $options[$id] = $this->getLayer($id, $space) . htmlspecialchars($this->getValue($id));
            }
        }
        return $options;
    }

    /**
     * 设置结点
     *
     * @param mix $id
     * @param mix $parent
     * @param mix $value
     */
    public function setNode($id, $parent, $value)
    {
        $parent = $parent ? $parent : 0;

        $this->data[$id] = $value;
        if (!isset($this->child[$id])) {
            $this->child[$id] = array();
        }

        if (isset($this->child[$parent])) {
            $this->child[$parent][] = $id;
        } else {
            $this->child[$parent] = array(
                $id
            );
        }

        $this->parent[$id] = $parent;
    }

    /**
     * 计算layer
     */
    public function setLayer($root = 0)
    {
        foreach ($this->child[$root] as $id) {
            $this->layer[$id] = $this->layer[$this->parent[$id]] + 1;
            if ($this->child[$id]) $this->setLayer($id);
        }
    }

    /**
     * 先根遍历，不包括root
     *
     * @param array $tree
     * @param mix $root
     * @param mix $except
     *        除外的结点，用于编辑结点时，上级不能选择自身及子结点
     */
    public function getList(&$tree, $root = 0, $except = NULL)
    {
        foreach ($this->child[$root] as $id) {
            if ($id == $except) {
                continue;
            }

            $tree[] = $id;

            if ($this->child[$id]) $this->getList($tree, $id, $except);
        }
    }

    public function getValue($id)
    {
        return $this->data[$id][$this->value_field];
    }

    public function getLayer($id, $space = false)
    {
        return $space ? str_repeat($space, $this->layer[$id]) : $this->layer[$id];
    }

    public function getParent($id)
    {
        return $this->parent[$id];
    }

    /**
     * 取得祖先，不包括自身
     *
     * @param mix $id
     * @return array
     */
    public function getParents($id)
    {
        while ($this->parent[$id] != -1) {
            $id = $parent[$this->layer[$id]] = $this->parent[$id];
        }

        ksort($parent);
        reset($parent);

        return $parent;
    }

    public function getChild($id)
    {
        return $this->child[$id];
    }

    /**
     * 取得子孙，包括自身，先根遍历
     *
     * @param int $id
     * @return array
     */
    public function getChilds($id = 0, $except = NULL)
    {
        $child = array(
            $id
        );
        $this->getList($child, $id, $except);
        unset($child[0]);

        return $child;
    }

    /**
     * 先根遍历，数组格式
     * array(
     * array('id' => '', 'value' => '', children => array(
     * array('id' => '', 'value' => '', children => array()),
     * ))
     * )
     */
    public function getArrayList($root = 0, $layer = NULL)
    {
        $data = array();
        foreach ($this->child[$root] as $id) {
            if ($layer && $this->layer[$this->parent[$id]] > $layer - 1) {
                continue;
            }
            $temp = array(
                $this->idKeyName => $id,
                $this->valueKeyName => $this->getValue($id),
//                $this->childrenKeyName => $this->child[$id] ? $this->getArrayList($id, $layer) : array()
            );
            if($this->child[$id]) {
                $temp[$this->childrenKeyName] = $this->getArrayList($id, $layer);
            } else {
                if($this->showChildrenKeyIfNull) {
                    $temp[$this->childrenKeyName] = array();
                }
            }
            $data[] = $temp;
        }
        return $data;
    }

    /**
     * 取得csv格式数据
     *
     * @param int $root
     * @param mix $ext_field
     *        辅助字段
     * @return array( array('辅助字段名','主字段名'), //如无辅助字段则无此元素
     *         array('辅助字段值','一级分类'), //如无辅助字段则无辅助字段值
     *         array('辅助字段值','一级分类'),
     *         array('辅助字段值','', '二级分类'),
     *         array('辅助字段值','', '', '三级分类'),
     *         )
     */
    public function getCSVData($root = 0, $ext_field = array())
    {
        $data = array();
        $main = $this->value_field; // 用于显示树分级结果的字段
        $extra = array(); // 辅助的字段
        if (!empty($ext_field)) {
            if (is_array($ext_field)) {
                $extra = $ext_field;
            } elseif (is_string($ext_field)) {
                $extra = array(
                    $ext_field
                );
            }
        }
        $childs = $this->getChilds($root);
        array_values($extra) && $data[0] = array_values($extra);
        $main && $data[0] && array_push($data[0], $main);
        foreach ($childs as $id) {
            $row = array();
            $value = $this->data[$id];
            foreach ($extra as $field) {
                $row[] = $value[$field];
            }
            for ($i = 1; $i < $this->getLayer($id); $i++) {
                $row[] = '';
            }
            if ($main) {
                $row[] = $value[$main];
            } else {
                $row[] = $value;
            }
            $data[] = $row;
        }
        return $data;
    }
}

/*
 * $Tree = new Tree();
   $Tree->setTree($res, 'id', 'parent_id', 'name');
   $data = $Tree->getArrayList(0);
 *
 *
 *
 * */