import 'package:jawara_pintar_pbl/models/rt.dart';


// Model untuk data RW
class Rw {
  final int rwId;
  final String nomorRw;
  final String namaKetuaRw;
  final List<Rt> rt;

  Rw({
    required this.rwId,
    required this.nomorRw,
    required this.namaKetuaRw,
    required this.rt,
  });

  factory Rw.fromJson(Map<String, dynamic> json) {
    var rtList = json['rt'] as List;
    List<Rt> rtItems = rtList.map((i) => Rt.fromJson(i)).toList();

    return Rw(
      rwId: json['rw_id'],
      nomorRw: json['nomor_rw'],
      namaKetuaRw: json['nama_ketua_rw'],
      rt: rtItems,
    );
  }
}
