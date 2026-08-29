import 'dart:convert';
import 'package:flutter/material.dart';
import '../core/api_client.dart';
import '../models/kehadiran_model.dart';

class KehadiranProvider with ChangeNotifier {
  List<Kehadiran> _listKehadiran = [];
  bool _isLoading = false;
  String _error = '';

  List<Kehadiran> get listKehadiran => _listKehadiran;
  bool get isLoading => _isLoading;
  String get error => _error;

  Future<void> fetchKehadiran() async {
    _isLoading = true;
    _error = '';
    notifyListeners();

    try {
      final response = await ApiClient.get('/kehadiran');
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body)['data'] as List;
        _listKehadiran = data.map((item) => Kehadiran.fromJson(item)).toList();
      } else {
        _error = 'Gagal mengambil data kehadiran';
      }
    } catch (e) {
      _error = 'Koneksi bermasalah: ${e.toString()}';
    }

    _isLoading = false;
    notifyListeners();
  }
}
