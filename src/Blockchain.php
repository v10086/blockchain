<?php
namespace v10086;
class Blockchain{

    //区块链表
    private $chain;
    
    //初始化 创建区块链 生成创世区块
    public function __construct(){
        $block = [
            'index' => 1,
            'timestamp' => time(),
            'transactions' =>'',//一般带上数字签名  公钥 以及交易信息等,  采用非对称加密技术 私钥加密交易数据得到签名 公钥解密签名获得交易数据 
            'proof' => 100,
            'prevBlockHash' => '0000000000000000000000000000000000000000000000000000000000000000',//参考BTC的第一个创世块
        ];
        $block['hash'] = $this->blockHash($block);
        $this->chain = [$block];
    }

    //获取区块hash签名
    private function blockHash($block){
        //确保这个字典（区块）是是按照规定顺序的，否则将会得到不一致的哈希值
        $blockArray = [
            'index' => $block['index'],
            'timestamp' => $block['timestamp'],
            'transactions' =>  $block['transactions'],
            'proof'        => $block['proof'],
            'prevBlockHash' => $block['prevBlockHash'],
        ];
        $blockString = json_encode($blockArray);
        return hash('sha256',$blockString);
    }

    //增加新区块
    public function addBlock(int $proof, string $transactions){
        //上一个区块的信息
        $preBlockInfo = $this->chain[count($this->chain)-1];
        //验证工作证明
        if($this->validProof($proof,$preBlockInfo['proof']) == false){
            return false;
        }
        
        $block = [
            'index'        => count($this->chain) + 1,
            'timestamp'    => time(),
            'transactions' => $transactions,
            'proof'        => $proof,
            'prevBlockHash' => $preBlockInfo['hash'],
            'hash'         => ''
        ];
        $block['hash'] = $this->getHash($block);
        //新增区块
        $this->chain[] = $block;
        //重置交易事务
        return true;
    }

    //校验算力
    private function validProof(string $proof,string $preProof){
        $string = $proof.$preProof;
        $hash   = hash('sha256',$string);
        if(substr($hash,0,4) == '0000'){
            return true;
        }else{
            return false;
        }
    }

    
    //挖矿
    public function mine($proof,$transactions){
        //获取最新区块
        $lastBlockInfo = $this->chain[count($this->chain)-1];
        if($this->validProof($proof, $lastBlockInfo['proof'])){
            //增加新区块
            $this->addBlock($proof,$transactions);
            return true;
        }else{
            return false;
        }  
    }

    //获取本区块链表
    public function getChain(){
        return $this->chain;
    }
    
}