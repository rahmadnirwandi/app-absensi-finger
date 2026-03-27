<?php

namespace App\Classes;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MyMigration extends Migration
{
    function set_table($table_name,$item){
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $getSchemaTable = Schema::getConnection()->getDoctrineSchemaManager();
            $table_column = $getSchemaTable->listTableColumns($table_name);
            $table_indexes = $getSchemaTable->listTableIndexes($table_name);
            $table_foreignKeys = $getSchemaTable->listTableForeignKeys($table_name);

            if (!Schema::hasTable($table_name)) {
                Schema::create($table_name, function (Blueprint $table_blue) use ($item) {
                    foreach($item->getColumns() as $key=>$value){
                        $value=$value->getAttributes();
                        $table_blue->addColumn($value['type'],$value['name'],$value);
                    }

                    foreach($item->getCommands() as $key=>$value){
                        $value=$value->getAttributes();
                        $value_tmp=(object)$value;
                        if($value['name']=='unique'){
                            $table_blue->unique($value['columns'],$value['index'],$value['algorithm']);
                        }

                        if($value['name']=='foreign'){
                            $table_blue->foreign($value['columns'],$value['index'])->references($value_tmp->references)->on($value_tmp->on)->onUpdate($value_tmp->onUpdate)->onDelete($value_tmp->onDelete);
                        }
                    }

                    if(!empty($item->charset)){
                        $table_blue->charset=$item->charset;
                    }else{
                        $table_blue->charset = 'latin1';
                    }

                    if(!empty($item->collation)){
                        $table_blue->collation=$item->collation;
                    }else{
                        $table_blue->collation = 'latin1_swedish_ci';
                    }

                });
            }else{
                $type_khusus=['timestamp','char'];
                Schema::table($table_name, function (Blueprint $table_blue) use ($item,$table_column,$table_indexes,$table_foreignKeys,$type_khusus) {

                    foreach($table_indexes as $key=>$value){

                        $check_unique=!empty($value->isUnique()) ? $value->isUnique() : false;
                        $check_primary=!empty($value->isPrimary()) ? $value->isPrimary() : false;

                        if( $check_unique==true and $check_primary==false ){
                            $table_blue->dropUnique($key);
                        }
                    }

                    foreach($table_foreignKeys as $key=>$value){
                        $table_blue->dropForeign($value->getName());
                    }

                    foreach($item->getColumns() as $key=>$value){
                        $value=$value->getAttributes();
                        if(array_key_exists($value['name'], $table_column)){
                            $value['change']=true;
                            $table_blue->removeColumn($value['name']);
                        }

                        $table_blue->addColumn($value['type'],$value['name'],$value);

                        // if(in_array($value['type'], $type_khusus)){
                            // $value['change']=false;
                            // $table_blue->removeColumn($value['name']);
                        // }else{
                            // $table_blue->addColumn($value['type'],$value['name'],$value);
                        // }

                        // $table_blue->addColumn($value['type'],$value['name'],$value);
                    }

                    foreach($item->getCommands() as $key=>$value){
                        $value=$value->getAttributes();
                        $value_tmp=(object)$value;
                        if($value['name']=='unique'){
                            $table_blue->unique($value['columns'],$value['index'],$value['algorithm']);
                        }

                        if($value['name']=='foreign'){
                            $table_blue->foreign($value['columns'],$value['index'])->references($value_tmp->references)->on($value_tmp->on)->onUpdate($value_tmp->onUpdate)->onDelete($value_tmp->onDelete);
                        }

                        if($value['name']=='primary'){
                            $table_blue->primary($value['columns']);
                        }
                    }

                    if(!empty($item->charset)){
                        $table_blue->charset=$item->charset;
                    }else{
                        $table_blue->charset = 'latin1';
                    }

                    if(!empty($item->collation)){
                        $table_blue->collation=$item->collation;
                    }else{
                        $table_blue->collation = 'latin1_swedish_ci';
                    }
                });
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Illuminate\Database\QueryException $e) {
            dd($e);
        } catch (\Throwable $e) {
            dd($e);
        }
    }

    function update_table($table_name,$item,$params=[]){

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $getSchemaTable = Schema::getConnection()->getDoctrineSchemaManager();
            $table_column = $getSchemaTable->listTableColumns($table_name);
            $table_indexes = $getSchemaTable->listTableIndexes($table_name);
            $table_foreignKeys = $getSchemaTable->listTableForeignKeys($table_name);

            if (Schema::hasTable($table_name)) {
                Schema::table($table_name, function (Blueprint $table_blue) use ($item,$table_column,$table_indexes,$table_foreignKeys,$params) {

                    $table_field=[];
                    foreach($table_column as $value){
                        $table_field[$value->getName()]=$value;
                    }

                    if(!empty($params['dropForeign'])){
                        $list_foreign=[];
                        $list_foreign_tmp=$params['dropForeign'];
                        foreach($list_foreign_tmp as $value){
                            $list_foreign[$value]=$value;
                        }
                        foreach($table_foreignKeys as $key=>$value){
                            $me_column=$value->getColumns();
                            $me_column=!empty($me_column[0]) ? $me_column[0] : '';
                            if(!empty($list_foreign[$me_column])){
                                $table_blue->dropForeign($value->getName());
                            }
                        }
                    }

                    if(!empty($params['dropPrimary'])){
                        $list_primary=[];
                        $list_primary_tmp=$params['dropPrimary'];
                        foreach($list_primary_tmp as $value){
                            $list_primary[$value]=$value;
                        }
                        foreach($table_indexes as $key=>$value){
                            $check_primary=!empty($value->isPrimary()) ? $value->isPrimary() : false;
                            if($check_primary==true){
                                $me_column=$value->getColumns();
                                $me_column=!empty($me_column[0]) ? $me_column[0] : '';
                                if(!empty($list_primary[$me_column])){
                                    $table_blue->dropPrimary($me_column);
                                }
                            }
                        }
                    }

                    foreach($item->getColumns() as $key=>$value){
                        $value=$value->getAttributes();
                        if(empty($table_field[$value['name']])){
                            $table_blue->addColumn($value['type'],$value['name'],$value);
                        }
                    }

                    foreach($item->getCommands() as $key=>$value){
                        $value=$value->getAttributes();
                        $value_tmp=(object)$value;
                        if($value['name']=='unique'){
                            $table_blue->unique($value['columns'],$value['index'],$value['algorithm']);
                        }

                        if($value['name']=='foreign'){
                            $table_blue->foreign($value['columns'],$value['index'])->references($value_tmp->references)->on($value_tmp->on)->onUpdate($value_tmp->onUpdate)->onDelete($value_tmp->onDelete);
                        }

                        if($value['name']=='primary'){
                            $table_blue->primary($value['columns']);
                        }
                    }
                });
            };
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
        } catch (\Illuminate\Database\QueryException $e) {
            dd($e);
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}