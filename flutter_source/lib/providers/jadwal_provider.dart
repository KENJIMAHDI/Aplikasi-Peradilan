import 'dart:convert';
import 'package:flutter/material.dart';
import '../core/api_client.dart';
import '../models/jadwal_sidang_model.dart';

class JadwalProvider with ChangeNotifier {
  List<JadwalSidang> _jadwals = [];
  bool _isLoading = false;
  String _error = '';

  List<JadwalSidang> get jadwals => _jadwals;
  bool get isLoading => _isLoading;
  String get error => _error;

  Future<void> fetchJadwal() async {
    _isLoading = true;
    _error = '';
    notifyListeners();

    try {
      final response = await ApiClient.get('/jadwal-sidang');
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body)['data'] as List;
        _jadwals = data.map((j) => JadwalSidang.fromJson(j)).toList();
      } else {
        _error = 'Gagal memuat jadwal';
      }
    } catch (e) {
      _error = e.toString();
    }

    _isLoading = false;
    notifyListeners();
  }
}
