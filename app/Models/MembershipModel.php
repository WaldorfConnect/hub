<?php

namespace App\Models;

use App\Entities\Membership;
use CodeIgniter\Model;

class MembershipModel extends Model
{
    protected $table = MEMBERSHIPS;
    protected $primaryKey = "id";
    protected $returnType = Membership::class;

    protected $allowedFields = [
        'user_id', 'group_id', 'status'
    ];
}


