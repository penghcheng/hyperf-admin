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
     * saveData
     * 创建/修改记录
     * @param $data 保存数据
     * @param bool $type 是否强制写入，适用于主键是规则生成情况
     * @return null
     */
    public function saveData($data, $type = false)
    {
        $id = null;
        $instance = $this->getModelInstance(get_called_class());

        if (isset($data['id']) && $data['id'] && !$type) {
            $id = $data['id'];
            unset($data['id']);
            $query = $instance->query()->find($id);
            foreach ($data as $k => $v) {
                $query->$k = $v;
            }
            $query->save();
        } else {
            foreach ($data as $k => $v) {
                if ($k === 'id') {
                    $id = $v;
                }
                $instance->$k = $v;
            }
            $instance->save();
            if (!$id) {
                $id = $instance->id;
            }
        }
        return $id;
    }

    /**
     * 根据条件获取结果
     * @param $where
     * @param bool $type 是否查询多条
     * @param array $select 显示的字段
     * @param string $order 排序方式
     * @return array
     */
    public function getDataByWhereForSelect($where, $type = false, $select = ['*'], $order = '')
    {
        $instance = $this->getModelInstance(get_called_class());

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

        if (is_array($select) && $select[0] != '*') {
            $instance->select($select);
        }

        if (!empty($order)) {
            $orderArr = explode(' ', $order);
            $instance->orderBy(reset($orderArr), end($orderArr));
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

        return $instance->where($where)->delete();
    }
}