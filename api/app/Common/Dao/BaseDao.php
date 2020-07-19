<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/29
 * Time: 10:53
 */

namespace App\Common\Dao;


use App\Constants\ErrorCode;

abstract class BaseDao
{
    /**
     * GetModelInstance
     * 获取模型类实例
     * @param $key
     * @return mixed
     */
    private function getModelInstance($key)
    {
        $key = substr($key, strripos($key, '\\') + 1);
        if (substr($key, -3) == 'Dao') {
            $key = ucfirst(substr($key, 0, strlen($key) - 3));
            $fileName = BASE_PATH . "/app/Model/{$key}.php";
            $className = "App\\Model\\{$key}";
            if (file_exists($fileName)) {
                return make($className);
            }
        }
        throw new \RuntimeException("模型{$key}不存在，文件不存在！", ErrorCode::SERVER_ERROR);
    }

    /**
     * getData
     * 通过主键id/ids获取信息
     * @param $id id/id数组
     * @param bool $useCache 是否使用模型缓存
     * @return array
     */
    public function find($id, $useCache = true)
    {
        $instance = $this->getModelInstance(get_called_class());

        if ($useCache === true) {
            $modelCache = is_array($id) ? $instance->findManyFromCache($id) : $instance->findFromCache($id);
            return isset($modelCache) && $modelCache ? $modelCache->toArray() : [];
        }
        $query = $instance->query()->find($id);
        return $query->toArray();
    }


    /**
     * 通过主键id/ids获取信息
     * @param $id id/id数组
     * @param array $select
     * @return array
     */
    public function findForSelect($id, $select = ['*'])
    {
        $instance = $this->getModelInstance(get_called_class());

        $query = $instance->query();
        if (is_array($select) && $select[0] != '*') {
            $query->select($select);
        }
        return $query->find($id)->toArray();
    }

    /**
     * 根据条件获取结果
     * @param $where
     * @param bool $type 是否查询多条
     * @param array $select 显示的字段
     * @param string $order 排序方式
     * @return array
     */
    public function getDataByWhereForSelect($where = [], $type = false, $select = ['*'], $order = [])
    {
        $instance = $this->getModelInstance(get_called_class());

        $instance = $this->getWhere($instance, $where);

        if (is_array($select) && $select[0] != '*') {
            $instance->select($select);
        }
        /*if (!empty($order) && is_string($order)) {
            $orderArr = explode(' ', $order);
            $instance->orderBy(reset($orderArr), end($orderArr));
        }*/

        if (!empty($order) && is_array($order)) {
            foreach ($order as $k => $v) {
                $instance->orderBy($k, $v);
            }
        }
        $query = $type ? $instance->get() : $instance->first();
        return empty($query) ? $query : $query->toArray();
    }

    /**
     * deleteByIds
     * 根据ids删除
     * User：YM
     * @param $ids 删除的主键ids
     * @return int
     */
    public function deleteByIds($ids)
    {
        $instance = $this->getModelInstance(get_called_class());

        return $instance->destroy($ids);
    }

    /**
     * deleteByWhere
     * @param array $where 删除的条件
     * @return mixed
     */
    public function deleteByWhere(array $where = [])
    {
        $instance = $this->getModelInstance(get_called_class());
        $instance = $this->getWhere($instance, $where);
        return $instance->delete();
    }

    /**
     * 获取一列的值
     * @param array $where
     * @param array $fields
     * @return mixed
     */
    public function pluck(array $where = [], array $fields)
    {
        $instance = $this->getModelInstance(get_called_class());
        $instance = $this->getWhere($instance, $where);
        return $instance->pluck(implode(',', $fields));
    }

    /**
     * 获取总条数
     * @param array $where
     * @return mixed
     */
    public function count($where = [])
    {
        $instance = $this->getModelInstance(get_called_class());
        $instance = $this->getWhere($instance, $where);
        return $instance->count();
    }

    /**
     * 获取分页数据
     * @param array $where 条件
     * @param int $pageSize 每页大小
     * @param int $currPage 当前页
     * @param array $select 显示字段
     * @param $order 排序, 字符串或者数组
     * @return array
     */
    public function paginator($where = [], int $pageSize = 10, int $currPage = 1, $select = ['*'], $order = [])
    {
        $instance = $this->getModelInstance(get_called_class());
        $instance = $this->getWhere($instance, $where);
        $totalCount = $instance->count();
        list($totalPage, $startCount) = page($totalCount, $pageSize, $currPage);

        /*if (!empty($order) && is_string($order)) {
            $orderArr = explode(' ', $order);
            $instance->orderBy(reset($orderArr), end($orderArr));
        }*/

        if (!empty($order) && is_array($order)) {
            foreach ($order as $k => $v) {
                $instance->orderBy($k, $v);
            }
        }
        $list = $instance->select($select)->offset($startCount)->limit($pageSize)->get();
        $result = [
            'totalCount' => $totalCount,
            'pageSize' => $pageSize,
            'totalPage' => $totalPage,
            'currPage' => $currPage,
            'list' => $list
        ];
        return $result;
    }

    /**
     * 返回where之后的Instance
     * @param $where
     * @param $instance
     * @return mixed
     */
    private function getWhere($instance, $where = [])
    {
        if (is_array($where) && !empty($where)) {
            foreach ($where as $k => $v) {
                if (is_array($v)) {
                    if (strtolower($v[0]) == 'in') {
                        $instance = $instance->whereIn($k, explode(',', $v[1]));
                    } else {
                        $instance = $instance->where($k, $v[0], $v[1]);
                    }
                } else {
                    $instance = $instance->where($k, $v);
                }
                //$instance = is_array($v) ? $instance->where($k, $v[0], $v[1]) : $instance->where($k, $v);
            }
        }
        return $instance;
    }
}