import 'package:flutter/material.dart';
import 'package:jawara_pintar_pbl/models/user.dart';
import 'package:jawara_pintar_pbl/services/auth_service.dart';
import 'package:jawara_pintar_pbl/services/secure_storage_service.dart';


class AuthProvider with ChangeNotifier {
  final AuthService _authService = AuthService();
  final SecureStorageService _storageService = SecureStorageService();

  String? _token;
  User? _user;

  bool get isAuth => _token != null;
  String? get token => _token;
  User? get user => _user;

  // Fungsi Login
  Future<void> login(String login, String password) async {
    try {
      final response = await _authService.login(login, password);
      _token = response['token'];
      _user = response['user'];

      await _storageService.saveToken(_token!);
      notifyListeners();
    } catch (error) {
      rethrow; // Biarkan UI (LoginScreen) yang menangani error
    }
  }

  // Coba auto-login (untuk SplashScreen)
  Future<bool> tryAutoLogin() async {
    final storedToken = await _storageService.getToken();
    if (storedToken == null) {
      return false; // Tidak ada token
    }

    try {
      // Validasi token dengan mengambil data user
      final user = await _authService.getMe(storedToken);
      _token = storedToken;
      _user = user;
      notifyListeners();
      return true;
    } catch (error) {
      // Token tidak valid/expire
      await logout(); // Hapus token
      return false;
    }
  }

  // Fungsi Logout
  Future<void> logout() async {
    _token = null;
    _user = null;
    await _storageService.deleteToken();
    notifyListeners();
  }
}
