<?php

namespace Zl\Compose\Search;

use Elasticsearch\ClientBuilder;
use Zl\Compose\Search\Kernel\Interfaces\SearchInterface;
use Elasticsearch\Client;

/**
 * curl
 * 1、 删除索引
 *  curl -X DELETE "localhost:9200/zxz/"
 * 2、 分析索引
 *  curl -X GET "localhost:9200/zxz/_analyze" -H 'Content-Type: application/json' -d
 * 3、 添加索引字段 并指定索引类型
 *  curl -X PUT "localhost:9200/zxz" -H 'Content-Type: application/json' -d'{"mappings" : {"zxz" : {"properties" : {"title" : {"type" : "text","index" : false}}}}}'
 * 
 *
 *
 */

/**
 * 待解决
 * 1、改变mapping 类型
 * 2、添加es字段
 */

/**
 * Class EsClient
 * @package Zl\Compose\Search
 * return
 *  took : 是查询花费的时间，毫秒单位
 */
class EsClient implements SearchInterface
{
    /**
     * @var
     */
    protected $config;
    /**
     * @var Client $es
     */
    protected $es;

    /**
     * EsClient constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @return $this
     */
    public function connect()
    {
        $this->es = ClientBuilder::create()
            ->setHosts($this->config['host'])
            ->setRetries(1)
            ->build();


//        $this->es->indices()->putMapping();
        return $this;

    }

    /**
     * @return $this
     */
    public function create($data)
    {
        return $this->es->index($data);
    }

    /**
     *
     */
    public function get($search)
    {
        $search = $this->es->search($search);

        if (!$search['timed_out'] && $search['hits']) {
            return $search['hits']['hits'];
        }
        return [];
    }

    public function delete($_index, $_type, $_id)
    {
        try {
            $ret = $this->es->delete([
                'id' => $_id,
                'index' => $_index,
                'type' => $_type,
                'client' => [
                    'ignore' => 404
                ]
            ]);

            return $ret['result'] != 'not_found';
        } catch (DocNotExistExp $exception) {
            zxzLog('doc not exist while data is' . json_encode(func_get_args()), 'exp');
            return true;
        }
    }


    public function exist($_index, $_type, $_id)
    {
        $ret = $this->es->exists([
            'id' => $_id,
            'index' => $_index,
            'type' => $_type,
            'client' => []
        ]);
        return $ret;
    }

    public function update($_index, $_type, $_id, $data)
    {
        $ret = $this->es->update([
            'id' => $_id,
            'index' => $_index,
            'type' => $_type,
            'body' => [
                'doc' => $data
            ]
        ]);
        return $ret;
    }

    public function search($params)
    {

        return $this->es->search($params);
    }
    /**
     *
     */
    public function isConnected()
    {
    }

    /**
     *
     */
    public function ping()
    {
    }

    /**
     *
     */
    public function close()
    {
    }

    /**
     *
     */
    public function isClose()
    {
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }
}