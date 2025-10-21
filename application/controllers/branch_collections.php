<?php
_auth();
$ui->assign('_application_menu', 'collection');
$ui->assign('_title', 'Branch Collection - ' . $config['CompanyName']);
$ui->assign('_st', 'Collection Management');

$action = $routes[1];
$user   = User::_info();
$ui->assign('user', $user);
if(!(has_access($user->roleid, 'collection'))) {
    r2(U."dashboard",'e',$_L['You do not have permission']);
}
switch ($action) {

    # ------------------------
    # LIST PAGE (loads tpl with DataTable)
    # ------------------------
    case 'list':


        // branches for filter dropdown
        // Prepare branches for dropdown (admins see all branches, others only their branch)
        if ($user->roleid == 0) {
            $branches = ORM::for_table('sys_accounts')->order_by_asc('account')->find_array();
        } else {
            $branches = ORM::for_table('sys_accounts')->where('id', $user->branch_id)->find_array();
        }
        $ui->assign('branches', $branches);
        // assets (adjust asset names as your project uses)
        $ui->assign('xheader', Asset::css(['modal']));
        $ui->assign('xfooter', Asset::js(['datatables.min', 'modal']));
        $ui->display('branch_collection/branch_collections_list.tpl');

        break;

    # ------------------------
    # Data for DataTable (AJAX)
    # ------------------------
    case 'ajax_list':
        // Use _get() helper (or $_GET) for DataTables ajax params / filters
        $date_from  = _get('date_from');
        $date_to    = _get('date_to');
        $branch_id  = _get('branch_id');
        $status     = _get('status');
        // $search     = _get('search');

        try {
            // build base query with computed fields
            // $q = ORM::for_table('branch_collections')->table_alias('c')
            //     ->select_many('c.*')
            //     ->select_expr('(SELECT alias FROM sys_accounts WHERE id = c.branch_id)', 'branch_name')
            //     ->select_expr('(SELECT fullname FROM sys_users WHERE id = c.created_by)', 'admin_name')
            //     ->select_expr('(SELECT IFNULL(SUM(amount_paid),0) FROM branch_handover_entries WHERE collection_id = c.id)', 'handover_amount');

            $q = ORM::for_table('branch_collections')->table_alias('c')
                ->select_many('c.*')
                ->select_expr('(SELECT alias FROM sys_accounts WHERE id = c.branch_id)', 'branch_name')
                ->select_expr('(SELECT fullname FROM sys_users WHERE id = c.created_by)', 'admin_name')
                ->select_expr('(
                    SELECT IFNULL(SUM(h.amount_paid),0)
                    FROM branch_handover_entries h
                    WHERE h.collection_id = c.id AND h.status = "Approved"
                )', 'handover_amount')
                ->select_expr('(
                    SELECT COUNT(*)
                    FROM branch_handover_entries h
                    WHERE h.collection_id = c.id AND h.status = "Pending"
                )', 'pending_count');


            if ($date_from) {
                $q->where_gte('c.collection_date', $date_from);
            }
            if ($date_to) {
                $q->where_lte('c.collection_date', $date_to);
            }
            if ($branch_id) {
                if ($user->roleid != 0) {
                    // for non-admins, force their branch
                    $branch_id = $user->branch_id;
                    $q->where('c.branch_id', $branch_id);
                }else{
                    $q->where('c.branch_id', $branch_id);
                }
            }
            if ($status) {
                $q->where('c.status', $status);
            }

            // if ($search) {
            //     // basic search against owner_remark and branch alias
            //     $s = '%' . $search . '%';
            //     $q->where_raw('(c.owner_remark LIKE :s OR EXISTS(SELECT 1 FROM sys_accounts sa WHERE sa.id = c.branch_id AND sa.alias LIKE :s))', ['s' => $s]);
            // }
            // if ($search) {
            //     // basic search against reference_no and owner_remark and branch alias
            //     $s = '%' . $search . '%';
            //     $q->where_raw('(c.reference_no LIKE :s OR c.owner_remark LIKE :s OR EXISTS(SELECT 1 FROM sys_accounts sa WHERE sa.id = c.branch_id AND sa.alias LIKE :s))', ['s' => $s]);
            // }

            $rows = $q->order_by_desc('c.updated_at')->find_array();

            $data = [];
            $i = 1;
            foreach ($rows as $r) {
                // $paid_total_all = (float)$r['paid_total_all'];
                // $remaining_all  = max(0, (float)$r['collected_amount'] - $paid_total_all);
                $pending_count = (int)$r['pending_count'];

                $status_label = $r['status']; // keep DB status
                if ($user->roleid == 0 && $pending_count > 0) {
                    // add a small badge to nudge the owner
                    $status_label .= ' <span class="label label-warning" title="Handover entries awaiting approval">Needs approval: ' . $pending_count . '</span>';
                }
                
                $id = $r['id'];
                $sr_no = $i++;
                $collection_id_display = $sr_no;
                // $collection_id_display = $sr_no . ' <small>#' . $id . '</small>';
                $branch = $r['branch_name'];
                $date = $r['collection_date'];
                $amount = number_format($r['collected_amount'], 2, '.', '');
                $handover_amount = number_format($r['handover_amount'], 2, '.', '');
                $status_label;
                $owner_remark = $r['owner_remark'];
                $created_at = $r['created_at'];
                $updated_at = $r['updated_at'];
                if ($user->roleid == 0) {
                    $actions = '<a href="' . U . 'branch_collections/modal_view_handovers/' . $id . '/" class="btn btn-xs btn-primary view_handovers"><i class="fa fa-eye"></i> ' . $_L['View'] . '</a> ';
                    $actions .= '<a href="' . U . 'branch_collections/modal_add_collection/' . $id . '/" class="btn btn-xs btn-info add_collection"><i class="fa fa-edit"></i> ' . $_L['Edit'] . '</a> ';
                    $actions .= '<a href="' . U . 'branch_collections/delete_collection/' . $id . '/" class="btn btn-xs btn-danger cdelete" data-id="' . $id . '"><i class="fa fa-trash"></i> ' . $_L['Delete'] . '</a>';
                } else{
                    $actions = '<a href="' . U . 'branch_collections/modal_view_handovers/' . $id . '/" class="btn btn-xs btn-primary view_handovers"><i class="fa fa-eye"></i> ' . $_L['View'] . '</a> ';
                        
                    // Show "Add Handover" only if there is money left AND not final status
                    // $final_statuses = ['Paid', 'Confirmed'];
                    // $can_add = ($remaining_all > 0) && !in_array($r['status'], $final_statuses, true);

                    // if ($can_add) {
                        $actions .= '<a href="' . U . 'branch_collections/modal_handover/' . $id . '/" class="btn btn-xs btn-success add_handover"><i class="fa fa-money"></i> ' . $_L['Add'] . '</a> ';
                    // }
                }

                $data[] = [
                    $collection_id_display,
                    $branch,
                    $date,
                    $amount,
                    $handover_amount,
                    $status_label,
                    $owner_remark,
                    $created_at,
                    $updated_at,
                    $pending_count,
                    $actions
                ];
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['data' => $data]);

        } catch (\Exception $ex) {
            // Always return valid JSON on error (helps DataTables show a message)
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['data' => [], 'error' => $ex->getMessage()]);
            exit;
        }
        break;


    # ------------------------
    # Modal: add/edit collection (GET)
    # ------------------------
    case 'modal_add_collection':
        $id = route(2);
        $collection = null;
        if ($id) {
            $collection = ORM::for_table('branch_collections')->find_one($id);
            if (!$collection) {
                echo 'Collection not found';
                exit;
            }
        }
        $branches = ORM::for_table('sys_accounts')->select('id')->select('alias')->find_array();
        $ui->assign('branches', $branches);
        $ui->assign('collection', $collection);
        $ui->assign('today', date('Y-m-d'));
        $ui->display('branch_collection/modal_add_collection.tpl');
        break;


    # ------------------------
    # Save collection (AJAX POST)
    # ------------------------
    case 'add_collection_post':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $collection_id = _post('collection_id');
        $branch_id = _post('branch_id');
        $collection_date = _post('collection_date');
        $amount = floatval(_post('amount'));
        // $reference_no = _post('reference_no');
        $note = _post('note');

        if (!$branch_id || !$collection_date || $amount <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
            exit;
        }

        if ($collection_id) {
            $collection = ORM::for_table('branch_collections')->find_one($collection_id);
            if (!$collection) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Collection not found']);
                exit;
            }
        } else {
            $collection = ORM::for_table('branch_collections')->create();
            $collection->created_by = $user->id;
            $collection->created_at = date('Y-m-d H:i:s');
            $collection->status = 'Pending';
        }

        $collection->branch_id = $branch_id;
        $collection->collection_date = $collection_date;
        $collection->collected_amount = $amount;
        $collection->owner_remark = $note;
        // $collection->reference_no = $reference_no;
        $collection->updated_at = date('Y-m-d H:i:s');
        $collection->save();

        // after saving, recompute status (safe)
        update_collection_status($collection->id);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        break;

    # ------------------------
    # Add handover payment (GET) / Save handover (POST)
    # ------------------------
    case 'modal_handover':
        $collection_id = route(2);
        $collection = ORM::for_table('branch_collections')->find_one($collection_id);

        if (!$collection) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Collection not found']);
            } else {
                echo 'Collection not found';
            }
            exit;
        }

        $cap = (float)$collection->collected_amount;
        $current_total_all = (float) ORM::for_table('branch_handover_entries')->where('collection_id', $collection_id)->sum('amount_paid');

        $remaining = $cap - $current_total_all;
        // Optional hard block if already fully handed over or final
        $final_statuses = ['Paid','Confirmed'];
        $can_add = ($remaining > 0) && !in_array($collection->status, $final_statuses, true);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Save handover
            $amount_paid = floatval(_post('amount_paid'));
            $paid_date = _post('paid_date');
            $note = _post('note');

            // var_dump($remaining); // debug
            if ($remaining <= 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Full amount already handed over. No further handovers allowed.']);
                exit;
            }
            // exit;
            if ($amount_paid > ($remaining + 0.0001)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'You can handover at most ₹' . number_format($remaining, 2) . ' for this collection.'
                ]);
                exit;
            }

            if ($amount_paid <= 0 || !$paid_date) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please provide valid inputs']);
                exit;
            }

            $handover = ORM::for_table('branch_handover_entries')->create();
            $handover->collection_id = $collection_id;
            $handover->amount_paid = $amount_paid;
            $handover->paid_by = $user->id;
            $handover->paid_date = $paid_date;
            $handover->note = $note;
            $handover->status = 'Pending';
            $handover->created_at = date('Y-m-d H:i:s');
            $handover->updated_at = date('Y-m-d H:i:s');
            $handover->save();

            // Update collection status based on handovers
            update_collection_status($collection_id);

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            // Show modal form
            $ui->assign('cap', $cap);
            $ui->assign('paid_all', $current_total_all);
            $ui->assign('remaining', $remaining);
            $ui->assign('can_add', $can_add);

            $ui->assign('collection', $collection);
            $ui->assign('today', date('Y-m-d'));
            $ui->display('branch_collection/modal_add_handover.tpl');
        }
        break;


    # ------------------------
    # Modal: view handovers list for a collection (GET)
    # ------------------------
    case 'modal_view_handovers':
        $collection_id = route(2);
        $collection = ORM::for_table('branch_collections')->find_one($collection_id);
        if (!$collection) {
            echo 'Collection not found';
            exit;
        }
        $handovers = ORM::for_table('branch_handover_entries')
            ->where('collection_id', $collection_id)
            ->order_by_desc('id')
            ->find_array();

        // Add paid_by_name for each handover so template doesn't need a Smarty modifier
        foreach ($handovers as &$h) {
            $paid_by = isset($h['paid_by']) ? $h['paid_by'] : null;
            $u = null;
            if ($paid_by) {
                $u = ORM::for_table('sys_users')->find_one($paid_by);
            }
            $h['paid_by_name'] = $u ? $u->fullname : '-';
        }
        // unset reference for safety
        unset($h);

        $ui->assign('collection', $collection);
        $ui->assign('handovers', $handovers);
        $ui->assign('user', $user);
        $ui->display('branch_collection/modal_view_handovers.tpl');
        break;


    # ------------------------
    # Approve handover (AJAX POST)
    # ------------------------
    case 'handover_approve':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        if ($user->roleid != 0) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $id = _post('id');
        $handover = ORM::for_table('branch_handover_entries')->find_one($id);
        if (!$handover) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Handover not found']);
            exit;
        }

        $handover->confirmed_by = $user->id;
        $handover->status = 'Approved';
        $handover->updated_at = date('Y-m-d H:i:s');
        $handover->save();

        // Update collection total & status
        update_collection_status($handover->collection_id);

        echo json_encode(['success' => true]);
        exit;
        
    # ------------------------
    # Delete handover (AJAX POST)
    # ------------------------
    case 'handover_delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $id = _post('id');
        $h = ORM::for_table('branch_handover_entries')->find_one($id);
        if (!$h) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Handover not found']);
            exit;
        }
        $collection_id = $h->collection_id;
        $h->delete();

        // recompute parent status
        update_collection_status($collection_id);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        break;


        # ------------------------
        # Delete collection (AJAX POST) + delete children
        # ------------------------
        case 'delete_collection':
            header('Content-Type: application/json; charset=utf-8');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request']); exit;
            }

            // Allow only super admin (roleid==0). Adjust if you want branch admins to delete their own.
            if ($user->roleid != 0) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
            }

            $id = (int) _post('id');
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Missing collection id']); exit;
            }

            $c = ORM::for_table('branch_collections')->find_one($id);
            if (!$c) {
                echo json_encode(['success' => false, 'message' => 'Collection not found']); exit;
            }

            // (Optional) If you want to enforce branch-scope even for non-owners, add a check here.
            // if ($user->roleid != 0 && (int)$c->branch_id !== (int)$user->branch_id) { ... }

            try {
                $db = ORM::get_db();
                $db->beginTransaction();

                // Delete children first (in case your DB doesn't have ON DELETE CASCADE)
                ORM::for_table('branch_handover_entries')
                    ->where('collection_id', $id)
                    ->delete_many();

                // Delete parent
                $c->delete();

                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Collection deleted']);
            } catch (Exception $e) {
                if (isset($db) && $db->inTransaction()) $db->rollBack();
                echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()]);
            }
            exit;



    # ------------------------
    default:
        echo 'action not defined';
        break;
}
