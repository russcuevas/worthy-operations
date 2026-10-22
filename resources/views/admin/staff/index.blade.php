@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User & Personnel Management')
@section('page-subtitle', 'Manage 627 Admins, HQ, Cluster Heads, Top Coordinators, Registry Officers, and Cluster assignments')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
        <form action="{{ route('admin.staff.index') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <select name="role" class="form-select" onchange="this.form.submit()" style="min-width: 170px;">
                <option value="">Filter Role (All)</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>627 (Admin)</option>
                <option value="hq" {{ request('role') === 'hq' ? 'selected' : '' }}>HQ</option>
                <option value="cluster_head" {{ request('role') === 'cluster_head' ? 'selected' : '' }}>Cluster Head</option>
                <option value="top_coor" {{ request('role') === 'top_coor' ? 'selected' : '' }}>Top Coor</option>
                <option value="registry_officer" {{ request('role') === 'registry_officer' ? 'selected' : '' }}>Registry Officer</option>
            </select>

            <select name="cluster" class="form-select" onchange="this.form.submit()" style="min-width: 170px;">
                <option value="">Filter Cluster (All)</option>
                @foreach($clusters as $c)
                    <option value="{{ $c }}" {{ request('cluster') === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['role', 'cluster', 'status']))
                <a href="{{ route('admin.staff.index') }}" class="btn-wa btn-wa-outline btn-wa-sm">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            @endif
        </form>

        <button type="button" class="btn-wa btn-wa-primary" data-modal-open="addStaffModal">
            <i class="bi bi-person-plus-fill"></i>
            <span>Register New User / Personnel</span>
        </button>
    </div>

    <div class="wa-card">
        <div class="wa-card-header">
            <div class="wa-card-title">
                <i class="bi bi-people-fill text-primary"></i>
                <span>Personnel Directory ({{ $users->count() }} Users)</span>
            </div>
        </div>
        <div class="wa-card-body" style="padding: 16px;">
            <div class="table-responsive">
                <table class="datatable table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Photo</th>
                            <th>Name & Username</th>
                            <th>Role & Level</th>
                            <th>Cluster</th>
                            <th>Position & Dept</th>
                            <th>Contact Info</th>
                            <th>Tasks Stats</th>
                            <th>Status</th>
                            <th style="text-align: right; width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td>
                                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="user-avatar-sm" style="width: 38px; height: 38px;">
                                </td>
                                <td>
                                    <div style="font-weight: 700; font-size: 14px; color: var(--color-deep-navy);">{{ $u->name }}</div>
                                    <div style="font-size: 11.5px; color: var(--color-primary-blue);">
                                        <code>@<span>{{ $u->username }}</span></code>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $u->role_badge }}">{{ $u->role_label }}</span>
                                </td>
                                <td>
                                    @if($u->cluster)
                                        <span class="badge badge-info" style="font-size: 11px;">{{ $u->cluster }}</span>
                                    @else
                                        <span style="font-size: 11.5px; color: var(--text-light);">No Cluster</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 12.5px; font-weight: 600; color: var(--color-deep-navy);">{{ $u->position ?? 'Personnel' }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">{{ $u->department ?? 'Operations' }}</div>
                                </td>
                                <td>
                                    <div style="font-size: 12px;">{{ $u->contact_number ?? '—' }}</div>
                                    <div style="font-size: 11px; color: var(--text-light);">{{ $u->email ?? '—' }}</div>
                                </td>
                                <td>
                                    <div style="font-size: 12px;">
                                        <strong>{{ $u->completed_tasks_count }}</strong> completed of <strong>{{ $u->assigned_tasks_count }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $u->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                        {{ ucfirst($u->status) }}
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                        <button type="button" class="btn-wa btn-wa-outline btn-wa-sm" onclick="openEditStaffModal({{ $u->id }})" title="Edit User">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        @if($u->id !== auth()->id())
                                            <button type="button" class="btn-wa btn-wa-danger btn-wa-sm" onclick="openDeleteStaffModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->username) }}', '{{ addslashes($u->role_label) }}')" title="Delete User">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        @else
                                            <button type="button" class="btn-wa btn-wa-outline btn-wa-sm" style="opacity: 0.5; cursor: not-allowed;" title="Current user account cannot be deleted" disabled>
                                                <i class="bi bi-lock"></i> Current
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= MODALS ================= -->

    <!-- 1. Add Employee Modal -->
    <div class="modal-backdrop-custom" id="addStaffModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header">
                <div class="wa-modal-title">
                    <i class="bi bi-person-plus-fill text-primary"></i>
                    <span>Register New Personnel Account</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data" id="addStaffForm">
                @csrf
                <div class="wa-modal-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                        <div class="form-group">
                            <label for="stf_name" class="form-label required">Full Name</label>
                            <input type="text" name="name" id="stf_name" class="form-control" required placeholder="e.g. Juan Dela Cruz">
                        </div>

                        <div class="form-group">
                            <label for="stf_username" class="form-label required">Username</label>
                            <input type="text" name="username" id="stf_username" class="form-control" required placeholder="e.g. juan" style="text-transform: lowercase;">
                        </div>

                        <div class="form-group">
                            <label for="stf_email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="stf_email" class="form-control" placeholder="juan@worthyacosta.com">
                        </div>

                        <div class="form-group">
                            <label for="stf_password" class="form-label required">Login Password</label>
                            <input type="password" name="password" id="stf_password" class="form-control" required minlength="6" placeholder="••••••••">
                        </div>

                        <div class="form-group">
                            <label for="stf_role" class="form-label required">Role Hierarchy</label>
                            <select name="role" id="stf_role" class="form-select" required>
                                <option value="admin">627 (Admin - Full System Access)</option>
                                <option value="hq">HQ (Assigns to Cluster Head, Top Coor, Officer)</option>
                                <option value="cluster_head">Cluster Head (Assigns to Top Coor, Officer)</option>
                                <option value="top_coor">Top Coor (Assigns to Officer)</option>
                                <option value="registry_officer" selected>Registry Officer (View own tasks & submit proofs)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="stf_cluster_select" class="form-label">Assigned Cluster</label>
                            <select id="stf_cluster_select" class="form-select" onchange="handleClusterChange('add')">
                                <option value="">-- No Cluster / Unassigned --</option>
                                <option value="Cluster 1">Cluster 1</option>
                                <option value="Cluster 2">Cluster 2</option>
                                <option value="Cluster 3">Cluster 3</option>
                                <option value="Cluster 4">Cluster 4</option>
                                <option value="Cluster 5">Cluster 5</option>
                                <option value="Cluster 6">Cluster 6 (Extra / Other)</option>
                            </select>
                            <input type="hidden" name="cluster" id="stf_cluster" value="">
                        </div>

                        <div class="form-group" id="stf_barangay_group" style="display: none;">
                            <label for="stf_barangay_select" class="form-label">Assigned Barangay</label>
                            <select id="stf_barangay_select" class="form-select" onchange="handleBarangayChange('add')">
                                <option value="">-- Select Barangay (Optional / All) --</option>
                            </select>
                            <div id="stf_custom_barangay_group" style="display: none; margin-top: 8px;">
                                <input type="text" id="stf_custom_barangay" class="form-control" placeholder="Specify Barangay name..." oninput="updateClusterValue('add')">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="stf_position" class="form-label">Job Position / Title</label>
                            <input type="text" name="position" id="stf_position" class="form-control" placeholder="e.g. Field Coordinator">
                        </div>

                        <div class="form-group">
                            <label for="stf_department" class="form-label">Department / Unit</label>
                            <input type="text" name="department" id="stf_department" class="form-control" placeholder="e.g. Field Operations">
                        </div>

                        <div class="form-group">
                            <label for="stf_contact_number" class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" id="stf_contact_number" class="form-control" placeholder="+63 9...">
                        </div>

                        <div class="form-group">
                            <label for="stf_status" class="form-label required">Account Status</label>
                            <select name="status" id="stf_status" class="form-select" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stf_avatar" class="form-label">Profile Photo (Optional)</label>
                        <input type="file" name="avatar" id="stf_avatar" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="wa-modal-footer">
                    <button type="button" class="btn-wa btn-wa-outline" data-modal-close>Cancel</button>
                    <button type="submit" class="btn-wa btn-wa-primary">Register Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Edit Employee Modal -->
    <div class="modal-backdrop-custom" id="editStaffModal">
        <div class="wa-modal-dialog modal-lg">
            <div class="wa-modal-header">
                <div class="wa-modal-title">
                    <i class="bi bi-pencil-square text-primary"></i>
                    <span>Edit Personnel Account</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="editStaffForm">
                @csrf
                @method('PUT')
                <div class="wa-modal-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                        <div class="form-group">
                            <label for="edit_stf_name" class="form-label required">Full Name</label>
                            <input type="text" name="name" id="edit_stf_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_stf_username" class="form-label required">Username</label>
                            <input type="text" name="username" id="edit_stf_username" class="form-control" required style="text-transform: lowercase;">
                        </div>

                        <div class="form-group">
                            <label for="edit_stf_email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="edit_stf_email" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="edit_stf_password" class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" name="password" id="edit_stf_password" class="form-control" minlength="6" placeholder="Leave blank to keep">
                        </div>

                        <div class="form-group">
                            <label for="edit_stf_role" class="form-label required">Role Hierarchy</label>
                            <select name="role" id="edit_stf_role" class="form-select" required>
                                <option value="admin">627 (Admin - Full System Access)</option>
                                <option value="hq">HQ (Assigns to Cluster Head, Top Coor, Officer)</option>
                                <option value="cluster_head">Cluster Head (Assigns to Top Coor, Officer)</option>
                                <option value="top_coor">Top Coor (Assigns to Officer)</option>
                                <option value="registry_officer">Registry Officer (View own tasks & submit proofs)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_stf_cluster_select" class="form-label">Assigned Cluster</label>
                            <select id="edit_stf_cluster_select" class="form-select" onchange="handleClusterChange('edit')">
                                <option value="">-- No Cluster / Unassigned --</option>
                                <option value="Cluster 1">Cluster 1</option>
                                <option value="Cluster 2">Cluster 2</option>
                                <option value="Cluster 3">Cluster 3</option>
                                <option value="Cluster 4">Cluster 4</option>
                                <option value="Cluster 5">Cluster 5</option>
                                <option value="Cluster 6">Cluster 6 (Extra / Other)</option>
                            </select>
                            <input type="hidden" name="cluster" id="edit_stf_cluster" value="">
                        </div>

                        <div class="form-group" id="edit_stf_barangay_group" style="display: none;">
                            <label for="edit_stf_barangay_select" class="form-label">Assigned Barangay</label>
                            <select id="edit_stf_barangay_select" class="form-select" onchange="handleBarangayChange('edit')">
                                <option value="">-- Select Barangay (Optional / All) --</option>
                            </select>
                            <div id="edit_stf_custom_barangay_group" style="display: none; margin-top: 8px;">
                                <input type="text" id="edit_stf_custom_barangay" class="form-control" placeholder="Specify Barangay name..." oninput="updateClusterValue('edit')">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_stf_position" class="form-label">Job Position / Title</label>
                            <input type="text" name="position" id="edit_stf_position" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="edit_stf_department" class="form-label">Department / Unit</label>
                            <input type="text" name="department" id="edit_stf_department" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="edit_stf_contact_number" class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" id="edit_stf_contact_number" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="edit_stf_status" class="form-label required">Account Status</label>
                            <select name="status" id="edit_stf_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_stf_avatar" class="form-label">Update Profile Photo</label>
                        <input type="file" name="avatar" id="edit_stf_avatar" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="wa-modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <button type="button" class="btn-wa btn-wa-danger btn-wa-sm" id="editModalDeleteBtn" style="display: none;" onclick="triggerDeleteFromEditModal()">
                            <i class="bi bi-trash"></i> Delete User
                        </button>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="btn-wa btn-wa-outline" data-modal-close>Cancel</button>
                        <button type="submit" class="btn-wa btn-wa-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Delete Confirmation Modal -->
    <div class="modal-backdrop-custom" id="deleteStaffModal">
        <div class="wa-modal-dialog" style="max-width: 480px;">
            <div class="wa-modal-header" style="border-bottom: 1px solid rgba(239, 68, 68, 0.2); background: #fef2f2;">
                <div class="wa-modal-title" style="color: #dc2626; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>Confirm User Deletion</span>
                </div>
                <button type="button" class="wa-modal-close" data-modal-close>&times;</button>
            </div>
            <form action="" method="POST" id="deleteStaffForm">
                @csrf
                @method('DELETE')
                <div class="wa-modal-body" style="padding: 24px 20px;">
                    <div style="text-align: center; margin-bottom: 18px;">
                        <div style="width: 58px; height: 58px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 14px;">
                            <i class="bi bi-person-x-fill"></i>
                        </div>
                        <h4 style="font-size: 16px; font-weight: 700; color: var(--color-deep-navy); margin-bottom: 8px;">
                            Permanently delete this user account?
                        </h4>
                        <p style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 0;">
                            You are about to delete <strong id="delete_user_display_name" style="color: var(--color-deep-navy);"></strong> 
                            (<code style="color: var(--color-primary-blue);">@<span id="delete_user_display_username"></span></code>, <span id="delete_user_display_role" class="badge badge-secondary" style="font-size: 11px;"></span>).
                        </p>
                    </div>

                    <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 12px 14px; font-size: 12.5px; color: #9f1239; display: flex; gap: 10px; align-items: flex-start;">
                        <i class="bi bi-shield-exclamation" style="font-size: 18px; line-height: 1; flex-shrink: 0; margin-top: 1px;"></i>
                        <div>
                            <strong>Warning:</strong> This action cannot be reversed. The user's account credentials, permissions, and direct task records will be permanently removed.
                        </div>
                    </div>
                </div>
                <div class="wa-modal-footer" style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-wa btn-wa-outline" data-modal-close>Cancel</button>
                    <button type="submit" class="btn-wa btn-wa-danger" id="confirmDeleteSubmitBtn">
                        <i class="bi bi-trash-fill"></i> Permanently Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const clusterBarangays = {
        'Cluster 1': ['Alion', 'Batangas 2', 'Lucanin', 'Townsite', 'Cabcaben', 'Mt. View'],
        'Cluster 2': ['Alas Asin 1', 'Alas Asin 2', 'Baseco', 'Sisiman'],
        'Cluster 3': ['Malaya', 'Maligaya'],
        'Cluster 4': ['Camaya', 'San Carlos', 'Poblacion'],
        'Cluster 5': ['Ipag', 'San Isidro', 'Balon Anito', 'Biaan'],
        'Cluster 6': ['N/A']
    };

    function handleClusterChange(prefix, selectedBarangay = null, customValue = null) {
        const clusterSelect = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_cluster_select`);
        const barangayGroup = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_barangay_group`);
        const barangaySelect = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_barangay_select`);
        const customGroup = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_custom_barangay_group`);
        const customInput = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_custom_barangay`);

        const cluster = clusterSelect ? clusterSelect.value : '';

        if (!cluster) {
            if (barangayGroup) barangayGroup.style.display = 'none';
            if (barangaySelect) barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            if (customGroup) customGroup.style.display = 'none';
            if (customInput) customInput.value = '';
            updateClusterValue(prefix);
            return;
        }

        if (barangayGroup) barangayGroup.style.display = 'block';
        if (barangaySelect) barangaySelect.innerHTML = '';

        if (cluster === 'Cluster 6') {
            const optNA = document.createElement('option');
            optNA.value = 'N/A';
            optNA.textContent = 'N/A';
            barangaySelect.appendChild(optNA);

            const optCustom = document.createElement('option');
            optCustom.value = '__custom__';
            optCustom.textContent = 'Specific Barangay / Custom...';
            barangaySelect.appendChild(optCustom);

            if (selectedBarangay === '__custom__' || (selectedBarangay && selectedBarangay !== 'N/A')) {
                barangaySelect.value = '__custom__';
                if (customGroup) customGroup.style.display = 'block';
                if (customInput) customInput.value = customValue || (selectedBarangay !== '__custom__' ? selectedBarangay : '');
            } else {
                barangaySelect.value = 'N/A';
                if (customGroup) customGroup.style.display = 'none';
                if (customInput) customInput.value = '';
            }
        } else {
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Select Barangay (Optional / All) --';
            barangaySelect.appendChild(defaultOpt);

            const list = clusterBarangays[cluster] || [];
            let matched = false;

            list.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b;
                opt.textContent = b;
                if (selectedBarangay && selectedBarangay.toLowerCase() === b.toLowerCase()) {
                    opt.selected = true;
                    matched = true;
                }
                barangaySelect.appendChild(opt);
            });

            // If selectedBarangay exists but not in standard list (e.g. legacy data)
            if (selectedBarangay && !matched && selectedBarangay !== '') {
                const legacyOpt = document.createElement('option');
                legacyOpt.value = selectedBarangay;
                legacyOpt.textContent = `${selectedBarangay} (Current)`;
                legacyOpt.selected = true;
                barangaySelect.appendChild(legacyOpt);
            }

            if (customGroup) customGroup.style.display = 'none';
            if (customInput) customInput.value = '';
        }

        updateClusterValue(prefix);
    }

    function handleBarangayChange(prefix) {
        const barangaySelect = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_barangay_select`);
        const customGroup = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_custom_barangay_group`);
        const customInput = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_custom_barangay`);

        if (barangaySelect && barangaySelect.value === '__custom__') {
            if (customGroup) customGroup.style.display = 'block';
            if (customInput) customInput.focus();
        } else {
            if (customGroup) customGroup.style.display = 'none';
            if (customInput) customInput.value = '';
        }

        updateClusterValue(prefix);
    }

    function updateClusterValue(prefix) {
        const clusterSelect = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_cluster_select`);
        const barangaySelect = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_barangay_select`);
        const customInput = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_custom_barangay`);
        const hiddenInput = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_cluster`);

        if (!hiddenInput) return;

        const cluster = clusterSelect ? clusterSelect.value : '';
        if (!cluster) {
            hiddenInput.value = '';
            return;
        }

        if (cluster === 'Cluster 6') {
            if (barangaySelect && barangaySelect.value === '__custom__') {
                const customVal = customInput ? customInput.value.trim() : '';
                hiddenInput.value = customVal ? `Cluster 6 - ${customVal}` : 'Cluster 6';
            } else if (barangaySelect && barangaySelect.value === 'N/A') {
                hiddenInput.value = 'Cluster 6 - N/A';
            } else {
                hiddenInput.value = 'Cluster 6';
            }
        } else {
            const barangay = barangaySelect ? barangaySelect.value : '';
            if (barangay) {
                hiddenInput.value = `${cluster} - ${barangay}`;
            } else {
                hiddenInput.value = cluster;
            }
        }
    }

    function setClusterInModal(prefix, rawCluster) {
        const clusterSelect = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_cluster_select`);
        const hiddenInput = document.getElementById(`${prefix === 'add' ? 'stf' : 'edit_stf'}_cluster`);

        if (!rawCluster) {
            if (clusterSelect) clusterSelect.value = '';
            handleClusterChange(prefix);
            if (hiddenInput) hiddenInput.value = '';
            return;
        }

        if (hiddenInput) hiddenInput.value = rawCluster;

        let clusterFound = '';
        let barangayPart = '';

        for (let i = 1; i <= 6; i++) {
            const cName = `Cluster ${i}`;
            if (rawCluster.startsWith(cName)) {
                clusterFound = cName;
                const remainder = rawCluster.substring(cName.length).trim();
                if (remainder.startsWith('-')) {
                    barangayPart = remainder.substring(1).trim();
                } else {
                    barangayPart = remainder;
                }
                break;
            }
        }

        if (clusterFound) {
            if (clusterSelect) clusterSelect.value = clusterFound;
            if (clusterFound === 'Cluster 6') {
                if (barangayPart === 'N/A' || !barangayPart) {
                    handleClusterChange(prefix, 'N/A');
                } else {
                    handleClusterChange(prefix, '__custom__', barangayPart);
                }
            } else {
                handleClusterChange(prefix, barangayPart);
            }
        } else {
            if (clusterSelect) clusterSelect.value = '';
            handleClusterChange(prefix);
            if (hiddenInput) hiddenInput.value = rawCluster;
        }
    }

    // Reset Add Form Cluster on modal open
    document.querySelectorAll('[data-modal-open="addStaffModal"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const form = document.getElementById('addStaffForm');
            if (form) form.reset();
            setClusterInModal('add', '');
        });
    });

    const currentAuthId = {{ auth()->id() }};
    let currentEditUser = null;

    function openDeleteStaffModal(userId, name, username, roleLabel) {
        const form = document.getElementById('deleteStaffForm');
        form.action = `/admin/staff/${userId}`;
        document.getElementById('delete_user_display_name').textContent = name;
        document.getElementById('delete_user_display_username').textContent = username;
        document.getElementById('delete_user_display_role').textContent = roleLabel || 'Personnel';

        openModal('deleteStaffModal');
    }

    function triggerDeleteFromEditModal() {
        if (!currentEditUser) return;
        closeModal('editStaffModal');
        openDeleteStaffModal(
            currentEditUser.id,
            currentEditUser.name,
            currentEditUser.username,
            currentEditUser.role_label
        );
    }

    function openEditStaffModal(userId) {
        fetch(`/admin/staff/${userId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const u = data.user;
                    currentEditUser = u;
                    document.getElementById('editStaffForm').action = `/admin/staff/${u.id}`;
                    document.getElementById('edit_stf_name').value = u.name;
                    document.getElementById('edit_stf_username').value = u.username;
                    document.getElementById('edit_stf_email').value = u.email || '';
                    document.getElementById('edit_stf_role').value = u.role;
                    setClusterInModal('edit', u.cluster || '');
                    document.getElementById('edit_stf_position').value = u.position || '';
                    document.getElementById('edit_stf_department').value = u.department || '';
                    document.getElementById('edit_stf_contact_number').value = u.contact_number || '';
                    document.getElementById('edit_stf_status').value = u.status;
                    document.getElementById('edit_stf_password').value = '';

                    const deleteBtn = document.getElementById('editModalDeleteBtn');
                    if (deleteBtn) {
                        deleteBtn.style.display = (u.id === currentAuthId) ? 'none' : 'inline-flex';
                    }

                    openModal('editStaffModal');
                } else {
                    showToast('Error', 'Could not load personnel information.', 'danger');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error', 'Failed to retrieve personnel data.', 'danger');
            });
    }
</script>
@endpush
