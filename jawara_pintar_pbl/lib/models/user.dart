import 'package:jawara_pintar_pbl/models/role.dart';
import 'package:jawara_pintar_pbl/models/warga.dart';

class User {
  final int userId;
  final String username;
  final String email;
  final Role role;
  final Warga? warga; // Warga bisa null (jika Admin)

  User({
    required this.userId,
    required this.username,
    required this.email,
    required this.role,
    this.warga,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      userId: json['user_id'],
      username: json['username'],
      email: json['email'],
      role: Role.fromJson(json['role']),
      warga: json['warga'] != null ? Warga.fromJson(json['warga']) : null,
    );
  }
}
