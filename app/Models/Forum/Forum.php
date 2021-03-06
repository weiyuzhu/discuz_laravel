<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Cache;
use DB;

class Forum extends Model
{
    public $table = "dz_forum_forum";

    public $primaryKey = "fid";

    /**
     * dz_forum_forum 中的1行，对应 dz_forum_forumfield中1行
     */
    public function forumfield()
    {
        return $this->belongsTo('App\Models\Forum\Field', 'fid', 'fid');
    }

    public function thread()
    {
        return $this->hasMany('App\Models\Forum\Thread', 'fid', 'fid');
    }

    /**
     * 获得uid用户加入的圈子的信息
     * @uid: int 用户id
     * @return: Eloquent Object
     */
    public function getForumsByUid($uid)
    {
        //
    }

    /**
     * 获得所有的圈子的信息，按照fid来索引
     * 经过再次检查发现这里如果改成ORM来操作数据
     * 智慧让代码复杂很多，对于管理抽象一点帮助都没有
     *
     * @return: array
     */
    public static function getForumInfo()
    {
        $key = "forumInfo";
        $data = Cache::remember($key, config('cache.forumttl'), function () {
            $f = "dz_forum_forum";
            $ff = "dz_forum_forumfield";
            $key = "forumInfo";
            $tmpData = DB::table($f)
                ->join($ff, $f.'.fid', '=', $ff.'.fid')
                ->select($f.'.fid', $f.'.name', $ff.'.icon')
                ->get();
            foreach ($tmpData as $k => $v) {
                $fid = $v->fid;
                $v->icon = \Attach::forumIconUrl($v->icon);
                $data[$fid] = $v;
            }
            return $data;
        });
        return $data;
    }
}
