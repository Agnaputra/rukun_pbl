// Model untuk data RT (Nested di dalam RW)
class Rt {
  final int rtId;
  final String nomorRt;
  final String namaKetuaRt;

  Rt({required this.rtId, required this.nomorRt, required this.namaKetuaRt});

  factory Rt.fromJson(Map<String, dynamic> json) {
    return Rt(
      rtId: json['rt_id'],
      nomorRt: json['nomor_rt'],
      namaKetuaRt: json['nama_ketua_rt'],
    );
  }
}
