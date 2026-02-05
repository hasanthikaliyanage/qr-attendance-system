<!DOCTYPE html>
<html>
<head>
    <title>Account Credentials</title>
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px; 
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header { 
            background: {{ $userType == 'Student' ? '#4F46E5' : ($userType == 'Lecturer' ? '#059669' : '#7C3AED') }}; 
            color: white; 
            padding: 30px 20px; 
            text-align: center; 
            border-radius: 10px 10px 0 0;
        }
        .content { 
            padding: 30px; 
        }
        .credentials { 
            background: #f8f9fa; 
            padding: 25px; 
            border-radius: 8px; 
            margin: 25px 0; 
            border-left: 4px solid {{ $userType == 'Student' ? '#4F46E5' : ($userType == 'Lecturer' ? '#059669' : '#7C3AED') }};
        }
        .footer { 
            text-align: center; 
            margin-top: 30px; 
            color: #666; 
            font-size: 12px; 
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .user-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            background: {{ $userType == 'Student' ? '#4F46E5' : ($userType == 'Lecturer' ? '#059669' : '#7C3AED') }};
            color: white;
        }
        .info-box {
            background: #f0f9ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e0f2fe;
        }
        .info-box h3 {
            margin-top: 0;
            color: #0369a1;
        }
        .login-button {
            display: inline-block;
            background: {{ $userType == 'Student' ? '#4F46E5' : ($userType == 'Lecturer' ? '#059669' : '#7C3AED') }};
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 15px 0;
        }
        .login-button:hover {
            opacity: 0.9;
        }
        .credentials-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .credentials-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .credentials-table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 28px;">University QR Attendance System</h1>
            @if($userType)
                <p style="margin: 10px 0 0 0; font-size: 18px; opacity: 0.9;">
                    {{ $userType }} Account
                </p>
            @endif
        </div>
        
        <div class="content">
            <h2 style="color: #333; margin-bottom: 10px;">Welcome, {{ $name }}!</h2>
            
            @if($userType)
                <div class="user-badge">
                    {{ $userType }} Account
                </div>
            @endif
            
            <p style="font-size: 16px; color: #555;">
                Your account has been successfully created in the University QR Attendance System.
            </p>
            
            <div class="credentials">
                <h3 style="color: #333; margin-top: 0;">Your Login Credentials:</h3>
                
                <table class="credentials-table">
                    @if($role && $role != $userType)
                    <tr>
                        <td>Account Type:</td>
                        <td>{{ $role }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Email Address:</td>
                        <td style="color: #1e40af; font-family: monospace;">{{ $email }}</td>
                    </tr>
                    <tr>
                        <td>Temporary Password:</td>
                        <td style="color: #dc2626; font-family: monospace; font-weight: bold;">{{ $password }}</td>
                    </tr>
                    <tr>
                        <td>System URL:</td>
                        <td>
                            <a href="{{ $loginUrl }}" style="color: #2563eb; text-decoration: none;">
                                {{ $loginUrl }}
                            </a>
                        </td>
                    </tr>
                </table>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="{{ $loginUrl }}" class="login-button">
                        Login to Your Account
                    </a>
                </div>
            </div>
            
            <div class="info-box">
                <h3>🔐 Security Instructions:</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>You <strong>MUST</strong> change your password on first login</li>
                    <li>Do not share your credentials with anyone</li>
                    <li>For security, please login and change your password immediately</li>
                    <li>Use a strong password with letters, numbers, and symbols</li>
                </ul>
            </div>
            
            @if($userType == 'Student')
            <div class="info-box" style="background: #f0f9ff;">
                <h3>🎓 Student Features:</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li><strong>QR Code Scanner</strong> - Mark attendance by scanning QR codes</li>
                    <li><strong>Attendance Tracking</strong> - View your attendance records</li>
                    <li><strong>Course Information</strong> - Access your subjects and schedules</li>
                    <li><strong>Profile Management</strong> - Update your personal information</li>
                </ul>
                <p style="margin: 10px 0 0 0; color: #0369a1;">
                    <strong>Note:</strong> You must be physically present in class to scan QR codes for attendance.
                </p>
            </div>
            @endif
            
            @if($userType == 'Lecturer')
            <div class="info-box" style="background: #f0fdf4;">
                <h3>👨‍🏫 Lecturer Features:</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li><strong>QR Session Creation</strong> - Generate QR codes for classes</li>
                    <li><strong>Attendance Management</strong> - View and manage student attendance</li>
                    <li><strong>Reports & Analytics</strong> - Export attendance data</li>
                    <li><strong>Subject Management</strong> - Manage your assigned subjects</li>
                </ul>
                <p style="margin: 10px 0 0 0; color: #065f46;">
                    <strong>Note:</strong> You can create QR sessions for your classes through the lecturer dashboard.
                </p>
            </div>
            @endif
            
            <div style="background: #fef3c7; padding: 15px; border-radius: 6px; margin: 25px 0; border-left: 4px solid #d97706;">
                <h3 style="color: #92400e; margin-top: 0;">⚠️ Important Notes:</h3>
                <ul style="margin: 10px 0; padding-left: 20px; color: #92400e;">
                    <li>This is an <strong>automated email</strong> - Do not reply to this message</li>
                    <li>If you didn't request this account, please contact the administration</li>
                    <li>Keep your login credentials secure and confidential</li>
                </ul>
            </div>
            
            <p style="color: #555; font-size: 15px;">
                If you encounter any issues logging in, please contact the university administration.
            </p>
            
            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong style="color: #1e40af;">University Administration</strong><br>
                <span style="color: #6b7280; font-size: 14px;">QR Attendance System</span>
            </p>
        </div>
        
        <div class="footer">
            <p style="margin: 5px 0;">
                This email was automatically generated by the University QR Attendance System.
            </p>
            <p style="margin: 5px 0;">
                &copy; {{ date('Y') }} University QR Attendance System. All rights reserved.
            </p>
            <p style="margin: 5px 0; font-size: 11px; color: #999;">
                Please do not reply to this email. For support, contact: support@university.edu
            </p>
        </div>
    </div>
</body>
</html>