import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:jawara_pintar_pbl/constants/api_config.dart';
import 'package:jawara_pintar_pbl/models/user.dart';
import 'package:jawara_pintar_pbl/utils/http_expection.dart';


class AuthService {
  // Fungsi untuk login
  Future<Map<String, dynamic>> login(String login, String password) async {
    final url = Uri.parse(ApiConfig.baseUrl + ApiConfig.login);
    try {
      final response = await http.post(
        url,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: json.encode({'login': login, 'password': password}),
      );

      final responseData = json.decode(response.body);

      if (response.statusCode != 200) {
        // Gagal login
        throw HttpException(responseData['message'] ?? 'Login Gagal');
      }

      // Login sukses
      return {
        'token': responseData['access_token'],
        'user': User.fromJson(responseData['user']),
      };
    } catch (error) {
      rethrow;
    }
  }

  // Fungsi untuk mengambil data user (/me)
  Future<User> getMe(String token) async {
    final url = Uri.parse(ApiConfig.baseUrl + ApiConfig.getMe);
    try {
      final response = await http.get(
        url,
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      final responseData = json.decode(response.body);

      if (response.statusCode != 200) {
        throw HttpException('Token tidak valid');
      }

      return User.fromJson(responseData);
    } catch (error) {
      rethrow;
    }
  }
}
