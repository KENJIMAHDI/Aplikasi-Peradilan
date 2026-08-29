import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'constants.dart';

class ApiClient {
  static const storage = FlutterSecureStorage();

  static Future<Map<String, String>> _getHeaders() async {
    String? token = await storage.read(key: 'auth_token');
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  static Future<http.Response> get(String endpoint) async {
    try {
      final headers = await _getHeaders();
      final response = await http.get(Uri.parse('${AppConstants.baseUrl}$endpoint'), headers: headers);
      return response;
    } on SocketException {
      throw Exception('Tidak ada koneksi internet');
    }
  }

  static Future<http.Response> post(String endpoint, Map<String, dynamic> body) async {
    try {
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('${AppConstants.baseUrl}$endpoint'),
        headers: headers,
        body: jsonEncode(body),
      );
      return response;
    } on SocketException {
      throw Exception('Tidak ada koneksi internet');
    }
  }
}
