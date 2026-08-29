import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import 'jadwal_sidang_screen.dart';
import 'kehadiran_screen.dart';
import 'user_list_screen.dart';

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () => Provider.of<AuthProvider>(context, listen: false).logout(),
          )
        ],
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text('Selamat Datang, ${user?.name}', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
          Text('Role: ${user?.role.toUpperCase()}', style: const TextStyle(color: Colors.grey)),
          const SizedBox(height: 24),
          
          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
            children: [
              _buildMenuCard(context, 'Jadwal Sidang', Icons.calendar_today, Colors.blue, const JadwalSidangScreen()),
              _buildMenuCard(context, 'Kehadiran Hakim', Icons.people, Colors.green, const KehadiranScreen()),
              _buildMenuCard(context, 'User Management', Icons.supervised_user_circle, Colors.purple, const UserListScreen()),
              _buildMenuCard(context, 'Perkara', Icons.folder, Colors.orange, null),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildMenuCard(BuildContext context, String title, IconData icon, Color color, Widget? target) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: InkWell(
        onTap: () {
          if (target != null) {
            Navigator.push(context, MaterialPageRoute(builder: (_) => target));
          } else {
            ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Segera Hadir')));
          }
        },
        borderRadius: BorderRadius.circular(16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircleAvatar(radius: 30, backgroundColor: color.withOpacity(0.1), child: Icon(icon, size: 30, color: color)),
            const SizedBox(height: 12),
            Text(title, style: const TextStyle(fontWeight: FontWeight.bold)),
          ],
        ),
      ),
    );
  }
}
