import 'package:flutter/material.dart';
import 'package:jawara_pintar_pbl/models/rw.dart';
import 'package:jawara_pintar_pbl/providers/auth_provider.dart';
import 'package:jawara_pintar_pbl/providers/rw_provider.dart';
import 'package:provider/provider.dart';


class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  void initState() {
    // Pindahkan logika fetch ke initState()
    // Ini berjalan SEBELUM build pertama
    // Penggunaan listen: false di sini sangat penting dan sudah benar
    Provider.of<RwProvider>(context, listen: false).fetchAndSetRws();
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    // Ambil data user dari AuthProvider
    final user = Provider.of<AuthProvider>(context).user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () {
              Provider.of<AuthProvider>(context, listen: false).logout();
            },
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Selamat Datang, ${user?.warga?.namaLengkap ?? user?.username}!',
              style: Theme.of(context).textTheme.headlineSmall,
            ),
            const SizedBox(height: 8),
            Text(
              'Role Anda: ${user?.role.namaRole}',
              style: Theme.of(context).textTheme.titleMedium,
            ),
            const SizedBox(height: 24),
            Text(
              'Daftar RW di Wilayah Anda:',
              style: Theme.of(context).textTheme.titleLarge,
            ),
            const SizedBox(height: 16),
            Expanded(
              child: Consumer<RwProvider>(
                builder: (ctx, rwData, child) {
                  // --- PERBAIKAN DIMULAI DI SINI ---

                  // 1. Jika sedang loading, TAMPILKAN SPINNER
                  if (rwData.isLoading) {
                    return const Center(child: CircularProgressIndicator());
                  }

                  // 2. Jika tidak loading TAPI data kosong, TAMPILKAN PESAN
                  if (rwData.items.isEmpty) {
                    return const Center(child: Text('Tidak ada data RW.'));
                  }

                  // 3. Jika tidak loading DAN ada data, BARU tampilkan list
                  return ListView.builder(
                    // 4. SELALU tambahkan itemCount
                    itemCount: rwData.items.length,
                    itemBuilder: (ctx, i) => _buildRwCard(rwData.items[i]),
                  );
                  // --- AKHIR PERBAIKAN ---
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Widget helper untuk build card RW
  Widget _buildRwCard(Rw rw) {
    return Card(
      elevation: 2,
      margin: const EdgeInsets.symmetric(vertical: 8),
      child: ListTile(
        leading: CircleAvatar(
          child: Text(
            rw.nomorRw,
            style: const TextStyle(fontWeight: FontWeight.bold),
          ),
        ),
        title: Text('Ketua: ${rw.namaKetuaRw}'),
        subtitle: Text('Total RT: ${rw.rt.length}'),
      ),
    );
  }
}
