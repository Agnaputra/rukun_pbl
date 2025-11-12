import 'package:flutter/material.dart';
import 'package:jawara_pintar_pbl/models/rw.dart';
import 'package:jawara_pintar_pbl/services/rw_service.dart';
// Ganti 'rukun_app' dengan nama proyek Anda jika berbeda


class RwProvider with ChangeNotifier {
  final RwService _rwService = RwService();
  final String? _token; // Token dari AuthProvider

  List<Rw> _items = [];
  bool _isLoading = false;

  RwProvider(this._token, this._items);

  List<Rw> get items => [..._items];
  bool get isLoading => _isLoading;

  Future<void> fetchAndSetRws() async {
    if (_token == null) return; // Tidak ada token, jangan lakukan apa-apa

    // --- PERBAIKAN DIMULAI DI SINI ---

    // 1. Set state di memori, tapi JANGAN notifikasi dulu
    _isLoading = true;

    // 2. Jadwalkan notifikasi untuk berjalan SETELAH build frame ini selesai.
    // Ini adalah trik untuk menghindari error "setState called during build".
    Future.delayed(Duration.zero, () {
      notifyListeners();
    });

    // --- AKHIR PERBAIKAN ---

    try {
      final rwList = await _rwService.fetchRws(_token!);
      _items = rwList;
    } catch (error) {
      // Tangani error, mungkin tampilkan pesan di UI
      _items = [];
    }
    _isLoading = false;
    notifyListeners(); // Panggilan notifikasi kedua ini aman karena terjadi setelah 'await'
  }
}
