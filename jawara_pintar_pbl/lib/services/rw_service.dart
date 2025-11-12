import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:jawara_pintar_pbl/constants/api_config.dart';
import 'package:jawara_pintar_pbl/models/rw.dart';
import 'package:jawara_pintar_pbl/utils/http_expection.dart';

class RwService {
  // Fungsi untuk mengambil daftar RW
  Future<List<Rw>> fetchRws(String token) async {
    final url = Uri.parse(ApiConfig.baseUrl + ApiConfig.rw);
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
        throw HttpException(responseData['message'] ?? 'Gagal mengambil data');
      }

      // Ambil list 'data' dari respon pagination
      final List<dynamic> dataList = responseData['data'];
      return dataList.map((rw) => Rw.fromJson(rw)).toList();
    } catch (error) {
      rethrow;
    }
  }
}
