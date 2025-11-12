class Warga {
  final int wargaId;
  final String nik;
  final String namaLengkap;
  final bool isVerifiedFace;

  Warga({
    required this.wargaId,
    required this.nik,
    required this.namaLengkap,
    required this.isVerifiedFace,
  });

  factory Warga.fromJson(Map<String, dynamic> json) {
    return Warga(
      wargaId: json['warga_id'],
      nik: json['nik'],
      namaLengkap: json['nama_lengkap'],
      // 'is_verified_face' adalah 0 atau 1 (int) di DB, konversi ke bool
      isVerifiedFace: json['is_verified_face'] == 1,
    );
  }
}
