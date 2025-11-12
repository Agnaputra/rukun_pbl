class Role {
  final int roleId;
  final String namaRole;

  Role({required this.roleId, required this.namaRole});

  factory Role.fromJson(Map<String, dynamic> json) {
    return Role(roleId: json['role_id'], namaRole: json['nama_role']);
  }
}
