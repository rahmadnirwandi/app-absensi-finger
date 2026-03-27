<?php

namespace App\Services\UserManagement;

use Illuminate\Support\Facades\DB;

use App\Models\UserManagement\UxuiAuthGroup;
use App\Models\UserManagement\UxuiAuthRoutes;
use App\Models\UserManagement\UxuiAuthPermission;
use App\Models\UxuiUsersKaryawan;
use App\Models\RefKaryawan;

class UserGroupAppService extends \App\Services\BaseService
{
    public $uxuiAuthGroup,$uxuiAuthRoutes,$uxuiAuthPermission,$uxuiUsersKaryawan;
    public $refKaryawan='';

    public function __construct() {
        $this->uxuiAuthGroup = new UxuiAuthGroup;
        $this->uxuiAuthRoutes = new UxuiAuthRoutes;
        $this->uxuiAuthPermission = new UxuiAuthPermission;
        $this->refKaryawan = new RefKaryawan;
        $this->uxuiUsersKaryawan = new UxuiUsersKaryawan;
    }

    function getUxuiAuthGroup($params = null)
    {
        $query = $this->uxuiAuthGroup->orderBy('id', 'DESC');

        if ($params) {
            foreach ($params as $key => $value) {

                if ($key != 'search') {
                    $type = is_numeric($value) ? '=' : 'like';
                    $query = $query->where($key, $type, $value);
                }
            }

            if (!empty($params['search'])) {
                $search = $params['search'];
                $query = $query->where(function ($qb2) use ($search) {
                    $qb2->where('name', 'LIKE', '%' . $search . '%')
                        ->where('alias', 'LIKE', '%' . $search . '%')
                        ->where('keterangan', 'LIKE', '%' . $search . '%');
                });
            }
        }
        return $query->get();
    }

    public function insertAuthGroup($data)
    {
        foreach ($data as $key => $field) {
            $field == null ? $data[$key] = "" : "";
        }
        return $this->uxuiAuthGroup->insert($data);
    }

    function updateAuthGroup($params, $set, $type = '')
    {
        
        $query = $this->uxuiAuthGroup;
        foreach ($params as $key => $value) {
            if ($type == 'raw') {
                $query = $query->whereRaw($key . ' = ? ', [$value]);
            } else {
                $query = $query->where($key, '=', $value);
            }
        }
        return $query->update($set);
    }

    function deleteAuthGroup($params, $type = '')
    {
        $query = $this->uxuiAuthGroup;
        foreach ($params as $key => $value) {
            if ($type == 'raw') {
                $query = $query->whereRaw($key . ' = ? ', [$value]);
            } else {
                $query = $query->where($key, '=', $value);
            }
        }
        return $query->delete();
    }

    public function getAllRouters()
    {
        return $this->uxuiAuthRoutes
            ->select(
                DB::raw(
                    "title, 
                            GROUP_CONCAT(url order by id separator ',') as urls,
                            GROUP_CONCAT(type order by id separator ',') as types,
                            GROUP_CONCAT(id order by id separator ',') as type_ids
                            "
                )
            )
            ->groupBy("title")
            ->orderBy('id')
            ->get();
    }

    public function getAllUserGroupAccess(string $alias)
    {
        $uar = $this->uxuiAuthRoutes->table;
        $uap = $this->uxuiAuthPermission->table;

        return $this->uxuiAuthPermission
            ->select($uar . ".id")
            ->join($uar, $uap . ".url", "=", $uar . ".url")
            ->where($uap . ".alias_group", "=", $alias)
            ->get()
            ->toArray();
    }

    public function getUserKaryawanGroup($params=[],$type=''){

        $query =DB::table(
            DB::raw('(
            select 
                ref_karyawan.*,
                nm_jabatan, 
                nm_departemen,
                uxui_users.id as id_uxui_users, 
                uxui_users.username, 
                alias_group, 
                uxui_auth_group.name as nama_group,
                IF ( uxui_users.id, 2, 1 ) status_tersedia,
                uxui_users.status as status_user,
                IF ( uxui_users.status=1, "aktif", "Tidak Aktif" ) nm_status_user
            from 
                ref_karyawan 
            left join ref_jabatan on ref_jabatan.id_jabatan = ref_karyawan.id_jabatan 
            left join ref_departemen on ref_departemen.id_departemen = ref_karyawan.id_departemen 
            left join uxui_users_karyawan on ref_karyawan.id_karyawan = uxui_users_karyawan.id_karyawan 
            left join uxui_users on uxui_users.id = uxui_users_karyawan.id_uxui_users 
            left join uxui_auth_users on uxui_auth_users.id_user = uxui_users.id 
            left join uxui_auth_group on uxui_auth_group.alias = uxui_auth_users.alias_group order by nm_karyawan asc) utama')
        );

        $list_search=[
            'where_or'=>['nm_karyawan','nip','nm_jabatan','nm_departemen','username'],
        ];

        if($params){
            $query=(new \App\Models\MyModel)->set_where($query,$params,$list_search);
        }

        if(empty($type)){
            return $query->get();
        }else{
            return $query;
        }
    }
}