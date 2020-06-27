<?php

namespace app\components;

use yii\web\GroupUrlRule;

class NestedSetUrlRule extends GroupUrlRule{

	public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();

        if (preg_match('/^link\//m', $pathInfo, $matches)) {
        	echo str_replace('link/', '', $pathInfo) ;
        	exit;
        }
        
        return false;
    }

}
?>