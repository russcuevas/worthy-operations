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
                            <th style="text-align: right; width: 80px;">Action</th>
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
                                <td style="text-align: right;">
                                    <button type="button" class="btn-wa btn-wa-outline btn-wa-sm" onclick="openEditStaffModal({{ $u->id }})">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
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
                            <label for="stf_cluster" class="form-label">Assigned Cluster</label>
                            <input type="text" name="cluster" id="stf_cluster" class="form-control" placeholder="e.g. Cluster 1 - Mariveles North">
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
                            <label for="edit_stf_cluster" class="form-label">Assigned Cluster</label>
                            <input type="text" name="cluster" id="edit_stf_cluster" class="form-control">
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
                <div class="wa-modal-footer">
                    <button type="button" class="btn-wa btn-wa-outline" data-modal-close>Cancel</button>
                    <button type="submit" class="btn-wa btn-wa-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openEditStaffModal(userId) {
        fetch(`/admin/staff/${userId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const u = data.user;
                    document.getElementById('editStaffForm').action = `/admin/staff/${u.id}`;
                    document.getElementById('edit_stf_name').value = u.name;
                    document.getElementById('edit_stf_username').value = u.username;
                    document.getElementById('edit_stf_email').value = u.email || '';
                    document.getElementById('edit_stf_role').value = u.role;
                    document.getElementById('edit_stf_cluster').value = u.cluster || '';
                    document.getElementById('edit_stf_position').value = u.position || '';
                    document.getElementById('edit_stf_department').value = u.department || '';
                    document.getElementById('edit_stf_contact_number').value = u.contact_number || '';
                    document.getElementById('edit_stf_status').value = u.status;
                    document.getElementById('edit_stf_password').value = '';

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
