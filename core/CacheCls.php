<?php
namespace Core;

class CacheCls{

    use Singleton;

    public function get(){
        return 'getCache';
    }

}