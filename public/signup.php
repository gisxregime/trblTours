<?php

declare(strict_types=1);

header('Location: /signup');
exit;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

defined('DB_HOST') || define('DB_HOST', '127.0.0.1');
defined('DB_PORT') || define('DB_PORT', '3306');
defined('DB_NAME') || define('DB_NAME', 'trbl_tours_db');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');

if (! function_exists('getDB')) {
    function getDB(): PDO
    {
        static $connection = null;

        if ($connection instanceof PDO) {
            return $connection;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT,
            DB_NAME
        );

        try {
            $connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return $connection;
        } catch (PDOException $exception) {
            throw new RuntimeException('Database connection failed: '.$exception->getMessage(), 0, $exception);
        }
    }
}

$errors = [];
$successMessage = '';

$allowedRoles = ['tourist', 'tour_guide'];
$allowedIdTypes = ['national_id', 'passport', 'drivers_license', 'other'];
$touristIdOptions = [
    'passport' => 'Passport',
    'national_id' => 'National ID',
    'drivers_license' => "Driver's License",
    'student_id' => 'Student ID',
    'postal_id' => 'Postal ID',
    'voters_id' => "Voter's ID",
    'umid' => 'UMID',
    'prc_id' => 'PRC ID',
    'barangay_id' => 'Barangay ID',
    'other_government_id' => 'Other Government ID',
];
$countryOptions = [
    'Filipino', 'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina',
    'Armenia', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus',
    'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei',
    'Bulgaria', 'Burkina Faso', 'Burundi', 'Cabo Verde', 'Cambodia', 'Cameroon', 'Canada',
    'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo',
    'Costa Rica', "Cote d'Ivoire", 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Democratic Republic of the Congo',
    'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador',
    'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland', 'France', 'Gabon',
    'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau',
    'Guyana', 'Haiti', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland',
    'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kuwait',
    'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania',
    'Luxembourg', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands',
    'Mauritania', 'Mauritius', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco',
    'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua', 'Niger',
    'Nigeria', 'North Korea', 'North Macedonia', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Panama',
    'Papua New Guinea', 'Paraguay', 'Peru', 'Poland', 'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda',
    'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino',
    'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore',
    'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Korea', 'South Sudan', 'Spain',
    'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania',
    'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan',
    'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay',
    'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe',
];

$requestedRole = isset($_GET['role']) && in_array((string) $_GET['role'], $allowedRoles, true)
    ? (string) $_GET['role']
    : 'tourist';

$formData = [
    'account_type' => $requestedRole,
    'full_name' => '',
    'email' => '',
    'email_verified' => false,
    'phone_number' => '',
    'phone_verified' => false,
    'date_of_birth' => '',
    'nationality' => 'Filipino',
    'government_id_type' => 'national_id',
    'government_id_number' => '',
    'tourist_id_type' => 'passport',
    'tourist_id_number' => '',
    'nbi_clearance_number' => '',
    'barangay_clearance_number' => '',
    'nbi_clearance_valid' => false,
    'years_of_experience' => '',
    'bio' => '',
    'tour_guide_cert_number' => '',
    'terms_agreed' => false,
    'identity_consent' => false,
    'pending_understood' => false,
];

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($requestMethod === 'POST') {
    $formData['account_type'] = in_array($_POST['account_type'] ?? 'tourist', $allowedRoles, true)
        ? (string) $_POST['account_type']
        : 'tourist';

    $formData['full_name'] = trim((string) ($_POST['full_name'] ?? ''));
    $formData['email'] = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');
    $formData['date_of_birth'] = trim((string) ($_POST['date_of_birth'] ?? ''));
    $formData['phone_number'] = trim((string) ($_POST['phone_number'] ?? ''));
    $formData['nationality'] = trim((string) ($_POST['nationality'] ?? ''));
    $formData['government_id_type'] = in_array((string) ($_POST['government_id_type'] ?? 'national_id'), $allowedIdTypes, true)
        ? (string) $_POST['government_id_type']
        : 'national_id';
    $formData['government_id_number'] = trim((string) ($_POST['government_id_number'] ?? ''));
    $formData['tourist_id_type'] = array_key_exists((string) ($_POST['tourist_id_type'] ?? ''), $touristIdOptions)
        ? (string) $_POST['tourist_id_type']
        : 'passport';
    $formData['tourist_id_number'] = trim((string) ($_POST['tourist_id_number'] ?? ''));
    $formData['nbi_clearance_number'] = trim((string) ($_POST['nbi_clearance_number'] ?? ''));
    $formData['barangay_clearance_number'] = trim((string) ($_POST['barangay_clearance_number'] ?? ''));
    $formData['nbi_clearance_valid'] = isset($_POST['nbi_clearance_valid']);
    $formData['email_verified'] = isset($_POST['email_verified']) && (string) $_POST['email_verified'] === '1';
    $formData['phone_verified'] = isset($_POST['phone_verified']) && (string) $_POST['phone_verified'] === '1';
    $formData['terms_agreed'] = isset($_POST['terms_agreed']);
    $formData['identity_consent'] = isset($_POST['identity_consent']);
    $formData['pending_understood'] = isset($_POST['pending_understood']);

    // Validation
    if ($formData['full_name'] === '') {
        $errors[] = 'Full Name is required.';
    }

    if ($formData['email'] === '' || filter_var($formData['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'A valid Email is required.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if ($password !== $passwordConfirmation) {
        $errors[] = 'Password confirmation does not match.';
    }

    if ($formData['date_of_birth'] === '') {
        $errors[] = 'Date of Birth is required.';
    }

    if ($formData['phone_number'] === '') {
        $errors[] = 'Phone Number is required.';
    }

    if ($formData['nationality'] === '') {
        $errors[] = 'Nationality is required.';
    }

    if (! $formData['email_verified']) {
        $errors[] = 'Email must be verified.';
    }

    if (! $formData['phone_verified']) {
        $errors[] = 'Phone number must be verified.';
    }

    if (! $formData['terms_agreed']) {
        $errors[] = 'You must agree to Terms & Privacy Policy.';
    }

    if ($formData['account_type'] === 'tourist') {
        if (! array_key_exists($formData['tourist_id_type'], $touristIdOptions)) {
            $errors[] = 'Please select a valid ID Type.';
        }

        if (! isset($_FILES['tourist_id_front_file']) || (int) $_FILES['tourist_id_front_file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Valid ID front upload is required for tourist registration.';
        }

        if (! isset($_FILES['tourist_id_back_file']) || (int) $_FILES['tourist_id_back_file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Valid ID back upload is required for tourist registration.';
        }
    }

    if ($formData['account_type'] === 'tour_guide') {
        $formData['years_of_experience'] = trim((string) ($_POST['years_of_experience'] ?? ''));
        $formData['bio'] = trim((string) ($_POST['bio'] ?? ''));
        $formData['tour_guide_cert_number'] = '';

        if ($formData['government_id_number'] === '') {
            $errors[] = 'Government ID Number is required.';
        }

        if (! isset($_FILES['id_front_file']) || (int) $_FILES['id_front_file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Government ID Front upload is required.';
        }

        if (! isset($_FILES['id_back_file']) || (int) $_FILES['id_back_file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Government ID Back upload is required.';
        }

        if (! isset($_FILES['selfie_file']) || (int) $_FILES['selfie_file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Selfie upload is required.';
        }

        if ($formData['years_of_experience'] === '' || filter_var($formData['years_of_experience'], FILTER_VALIDATE_INT) === false) {
            $errors[] = 'Years of Experience must be a valid number.';
        }

        if ($formData['bio'] === '') {
            $errors[] = 'Bio is required.';
        }

        if ($formData['nbi_clearance_number'] === '') {
            $errors[] = 'NBI Clearance Number is required.';
        }

        if (! isset($_FILES['nbi_clearance_file']) || (int) $_FILES['nbi_clearance_file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'NBI Clearance upload is required.';
        }

        if (! isset($_FILES['barangay_clearance_file']) || (int) $_FILES['barangay_clearance_file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Barangay Clearance upload is required.';
        }

        if (! $formData['identity_consent']) {
            $errors[] = 'You must consent to identity verification.';
        }

        if (! $formData['pending_understood']) {
            $errors[] = 'You must acknowledge the pending approval process and NBI Clearance review.';
        }

        if (! $formData['nbi_clearance_valid']) {
            $errors[] = 'You must confirm your NBI Clearance is valid.';
        }
    }

    if ($errors === []) {
        try {
            $db = getDB();
            $verifiedTimestamp = date('Y-m-d H:i:s');

            $emailCheck = $db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $emailCheck->execute([':email' => $formData['email']]);

            if ($emailCheck->fetch() !== false) {
                $errors[] = 'This email is already registered.';
            } else {
                $db->beginTransaction();

                $userRole = $formData['account_type'];
                $storedRole = $userRole;
                $userStatus = $storedRole === 'tour_guide' ? 'pending' : 'active';

                $userInsert = $db->prepare(
                    'INSERT INTO users (name, full_name, email, email_verified_at, phone_verified_at, password, role, status) VALUES (:name, :full_name, :email, :email_verified_at, :phone_verified_at, :password, :role, :status)'
                );

                $userInsert->execute([
                    ':name' => $formData['full_name'],
                    ':full_name' => $formData['full_name'],
                    ':email' => $formData['email'],
                    ':email_verified_at' => $formData['email_verified'] ? $verifiedTimestamp : null,
                    ':phone_verified_at' => $formData['phone_verified'] ? $verifiedTimestamp : null,
                    ':password' => password_hash($password, PASSWORD_DEFAULT),
                    ':role' => $storedRole,
                    ':status' => $userStatus,
                ]);

                $userId = (int) $db->lastInsertId();

                if ($userRole === 'tourist') {
                    $documentDirectory = 'signup-documents/tourists/'.$userId;
                    $touristIdFrontPath = storeUploadedSignupFile('tourist_id_front_file', $documentDirectory);
                    $touristIdBackPath = storeUploadedSignupFile('tourist_id_back_file', $documentDirectory);

                    $touristInsert = $db->prepare(
                        'INSERT INTO tourists_profile (user_id, phone_number, nationality, date_of_birth, tourist_id_type, tourist_id_number, id_front_path, id_back_path, selfie_path, id_front_verified, id_back_verified, selfie_verified, terms_agreed, identity_consent, pending_understood) VALUES (:user_id, :phone_number, :nationality, :date_of_birth, :tourist_id_type, :tourist_id_number, :id_front_path, :id_back_path, :selfie_path, :id_front_verified, :id_back_verified, :selfie_verified, :terms_agreed, :identity_consent, :pending_understood)'
                    );

                    $touristInsert->execute([
                        ':user_id' => $userId,
                        ':phone_number' => $formData['phone_number'],
                        ':nationality' => $formData['nationality'],
                        ':date_of_birth' => $formData['date_of_birth'],
                        ':tourist_id_type' => $formData['tourist_id_type'],
                        ':tourist_id_number' => $formData['tourist_id_number'],
                        ':id_front_path' => $touristIdFrontPath,
                        ':id_back_path' => $touristIdBackPath,
                        ':selfie_path' => null,
                        ':id_front_verified' => 0,
                        ':id_back_verified' => 0,
                        ':selfie_verified' => 0,
                        ':terms_agreed' => $formData['terms_agreed'] ? 1 : 0,
                        ':identity_consent' => $formData['identity_consent'] ? 1 : 0,
                        ':pending_understood' => $formData['pending_understood'] ? 1 : 0,
                    ]);
                }

                if ($userRole === 'tour_guide') {
                    $documentDirectory = 'signup-documents/tour-guides/'.$userId;
                    $idFrontPath = storeUploadedSignupFile('id_front_file', $documentDirectory);
                    $idBackPath = storeUploadedSignupFile('id_back_file', $documentDirectory);
                    $selfiePath = storeUploadedSignupFile('selfie_file', $documentDirectory);
                    $nbiClearancePath = storeUploadedSignupFile('nbi_clearance_file', $documentDirectory);
                    $barangayClearancePath = storeUploadedSignupFile('barangay_clearance_file', $documentDirectory);

                    $guideInsert = $db->prepare(
                        'INSERT INTO tour_guides_profile (user_id, phone_number, nationality, date_of_birth, years_of_experience, bio, government_id_type, government_id_number, id_front_path, id_back_path, selfie_path, tour_guide_cert_number, nbi_clearance_number, nbi_clearance_path, barangay_clearance_number, barangay_clearance_path, nbi_clearance_validated, id_front_verified, id_back_verified, selfie_verified, approved_by_admin, terms_agreed, identity_consent, pending_understood) VALUES (:user_id, :phone_number, :nationality, :date_of_birth, :years_of_experience, :bio, :government_id_type, :government_id_number, :id_front_path, :id_back_path, :selfie_path, :tour_guide_cert_number, :nbi_clearance_number, :nbi_clearance_path, :barangay_clearance_number, :barangay_clearance_path, :nbi_clearance_validated, :id_front_verified, :id_back_verified, :selfie_verified, :approved_by_admin, :terms_agreed, :identity_consent, :pending_understood)'
                    );

                    $guideInsert->execute([
                        ':user_id' => $userId,
                        ':phone_number' => $formData['phone_number'],
                        ':nationality' => $formData['nationality'],
                        ':years_of_experience' => (int) $formData['years_of_experience'],
                        ':bio' => $formData['bio'],
                        ':date_of_birth' => $formData['date_of_birth'],
                        ':government_id_type' => $formData['government_id_type'],
                        ':government_id_number' => $formData['government_id_number'],
                        ':id_front_path' => $idFrontPath,
                        ':id_back_path' => $idBackPath,
                        ':selfie_path' => $selfiePath,
                        ':tour_guide_cert_number' => $formData['tour_guide_cert_number'],
                        ':nbi_clearance_number' => $formData['nbi_clearance_number'],
                        ':nbi_clearance_path' => $nbiClearancePath,
                        ':barangay_clearance_number' => $formData['barangay_clearance_number'],
                        ':barangay_clearance_path' => $barangayClearancePath,
                        ':nbi_clearance_validated' => $formData['nbi_clearance_valid'] ? 1 : 0,
                        ':id_front_verified' => 0,
                        ':id_back_verified' => 0,
                        ':selfie_verified' => 0,
                        ':approved_by_admin' => 0,
                        ':terms_agreed' => $formData['terms_agreed'] ? 1 : 0,
                        ':identity_consent' => $formData['identity_consent'] ? 1 : 0,
                        ':pending_understood' => $formData['pending_understood'] ? 1 : 0,
                    ]);
                }

                $db->commit();

                $successMessage = $userRole === 'tour_guide'
                    ? 'Registration successful! Your NBI Clearance and identity documents are under review. Approval takes 2-3 business days.'
                    : 'Registration successful! Redirecting to login...';

                header('Refresh: 3; URL=/login');
            }
        } catch (Throwable $exception) {
            if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
                $db->rollBack();
            }

            $errors[] = 'Registration failed: '.$exception->getMessage();
        }
    }
}

function oldValue(array $data, string $key): string
{
    return htmlspecialchars((string) ($data[$key] ?? ''), ENT_QUOTES, 'UTF-8');
}

function selected(string $a, string $b): string
{
    return $a === $b ? 'selected' : '';
}

function storeUploadedSignupFile(string $fieldName, string $directory): ?string
{
    if (! isset($_FILES[$fieldName]) || (int) $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $baseDirectory = __DIR__.'/../storage/app/private';
    $targetDirectory = $baseDirectory.'/'.$directory;

    if (! is_dir($targetDirectory) && ! mkdir($targetDirectory, 0775, true) && ! is_dir($targetDirectory)) {
        throw new RuntimeException('Unable to create upload directory.');
    }

    $originalName = (string) $_FILES[$fieldName]['name'];
    $safeBaseName = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
    $safeBaseName = $safeBaseName !== '' ? $safeBaseName : 'document';
    $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
    $finalExtension = $extension !== '' ? $extension : 'bin';
    $fileName = sprintf('%s-%s.%s', $safeBaseName, bin2hex(random_bytes(6)), $finalExtension);
    $destination = $targetDirectory.'/'.$fileName;

    if (! move_uploaded_file((string) $_FILES[$fieldName]['tmp_name'], $destination)) {
        throw new RuntimeException('Unable to store uploaded file.');
    }

    return $directory.'/'.$fileName;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - TrblTours</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Asimovian:wght@400;700&family=Cormorant+SC:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .brand-font {
            font-family: 'Asimovian', 'Instrument Sans', sans-serif;
            letter-spacing: 0.02em;
        }

        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(212, 165, 99, 0.72), rgba(184, 195, 136, 0.56)), url('/images/signup_login_bg.jpg') center/cover no-repeat fixed;
            color: #6f5d52;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }

        .brand-header {
            position: fixed;
            top: clamp(10px, 2.2vw, 24px);
            left: clamp(10px, 2.2vw, 24px);
            display: flex;
            align-items: center;
            gap: clamp(8px, 1vw, 12px);
            z-index: 100;
        }

        .brand-icon {
            width: clamp(34px, 4.2vw, 50px);
            height: clamp(34px, 4.2vw, 50px);
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .brand-text {
            font-size: clamp(14px, 1.8vw, 20px);
            font-weight: 700;
            color: #fffaf0;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5), 0 4px 12px rgba(0, 0, 0, 0.4);
            letter-spacing: 0.02em;
        }

        .container {
            width: 100%;
            max-width: 700px;
            background: rgba(255, 251, 244, 0.98);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(101, 67, 33, 0.22);
            padding: 44px;
            backdrop-filter: blur(10px);
            max-height: calc(100vh - 64px);
            overflow-y: auto;
            border: 1px solid rgba(139, 69, 19, 0.08);
            margin: 24px 0;
            scrollbar-width: thin;
            scrollbar-color: #a36f3f rgba(242, 231, 207, 0.58);
            -ms-overflow-style: auto;
        }

        .container,
        form,
        input,
        select,
        textarea,
        button {
            min-width: 0;
        }

        .container::-webkit-scrollbar {
            width: 10px;
            height: 10px;
            display: block;
        }

        .container::-webkit-scrollbar-button {
            width: 0;
            height: 0;
            display: none;
            background: transparent;
        }

        .container::-webkit-scrollbar-button:single-button,
        .container::-webkit-scrollbar-button:start:decrement,
        .container::-webkit-scrollbar-button:start:increment,
        .container::-webkit-scrollbar-button:end:decrement,
        .container::-webkit-scrollbar-button:end:increment {
            display: none;
            width: 0;
            height: 0;
            background: transparent;
            border: none;
            box-shadow: none;
        }

        .container::-webkit-scrollbar-track {
            background: rgba(242, 231, 207, 0.58);
            border-radius: 999px;
            border: 1px solid rgba(139, 69, 19, 0.12);
            box-shadow: inset 0 1px 3px rgba(101, 67, 33, 0.08);
        }

        .container::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #c58b52 0%, #8B4513 55%, #6f5d52 100%);
            border-radius: 999px;
            border: 2px solid rgba(255, 250, 240, 0.88);
            box-shadow: 0 2px 10px rgba(101, 67, 33, 0.2);
            transition: background 0.2s ease, box-shadow 0.2s ease;
        }

        .container::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #d4a563 0%, #8B4513 52%, #5a4a42 100%);
            box-shadow: 0 3px 12px rgba(101, 67, 33, 0.3);
        }

        .container::-webkit-scrollbar-thumb:active {
            background: linear-gradient(180deg, #8B4513 0%, #6f5d52 100%);
        }

        .container::-webkit-scrollbar-corner {
            background: transparent;
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #8B4513;
            text-decoration: none;
            margin-bottom: 24px;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #6f5d52;
        }

        h1 {
            margin-bottom: 8px;
            font-size: 28px;
            color: #2a2a2a;
        }

        .subtitle {
            margin-bottom: 28px;
            color: #87756a;
            font-size: 14px;
        }

        .role-toggle {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 30px;
        }

        .role-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            border: 2px solid #e0d5c7;
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            background: #f9f6f0;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            color: #6f5d52;
        }

        .role-btn:hover {
            border-color: #d0bfb0;
            background: #f5ede2;
        }

        .role-btn.active {
            border-color: #8B4513;
            background: #fff7eb;
            color: #8B4513;
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.15);
        }

        .role-icon {
            font-size: 32px;
            color: #8B4513;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: rgba(139, 69, 19, 0.1);
            border-radius: 8px;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
        }

        .role-icon i {
            display: inline-block;
            font-size: 28px;
            line-height: 1;
            color: #8B4513;
            vertical-align: middle;
        }

        .info-banner {
            background: #f8f1e3;
            border: 1px solid #e5d39f;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #6f5d52;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .info-banner i {
            color: #8B4513;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .alert {
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert.error strong {
            display: block;
            margin-bottom: 4px;
        }

        .alert.error ul {
            margin: 6px 0 0 16px;
        }

        .alert.success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        form {
            display: grid;
            gap: 16px;
        }

        .form-section-title {
            font-weight: 700;
            color: #5a4a42;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 22px;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0d5c7;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        label {
            font-weight: 600;
            font-size: 13px;
            color: #5a4a42;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .required::after {
            content: '*';
            color: #ef4444;
            margin-left: 4px;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid #e0d5c7;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            color: #6f5d52;
            background: #fffef9;
            transition: border-color 0.2s ease;
            font-family: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #8B4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        input::placeholder {
            color: #b8a89c;
        }

        textarea {
            min-height: 80px;
            resize: vertical;
        }

        .grid-2 {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(2, 1fr);
        }

        .verification-group {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }

        .verification-group input {
            flex: 1;
        }

        .verify-btn {
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            background: #8B4513;
            color: white;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .verify-btn:hover {
            background: #6f5d52;
        }

        .verify-btn:disabled {
            background: #d4a59b;
            cursor: not-allowed;
        }

        .otp-input {
            max-width: 120px;
        }

        .file-upload-wrapper {
            border: 2px dashed #e5d39f;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fffaf3;
            min-height: 88px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .file-upload-wrapper:hover {
            border-color: #8B4513;
            background: #fff5e8;
        }

        .file-upload-wrapper input[type="file"] {
            display: none;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #6f5d52;
            width: 100%;
            z-index: 2;
        }

        .file-upload-label i {
            font-size: 26px;
            color: #8B4513;
        }

        .file-upload-wrapper.has-preview {
            padding: 0;
            min-height: 116px;
            border-style: solid;
        }

        .file-upload-wrapper.has-preview .file-upload-label > i,
        .file-upload-wrapper.has-preview .file-upload-label > span {
            display: none;
        }

        .upload-inline-preview {
            width: 100%;
            height: 116px;
            object-fit: cover;
            display: block;
            border-radius: 6px;
        }

        .upload-inline-file {
            min-height: 116px;
            display: grid;
            place-items: center;
            gap: 8px;
            color: #6f5d52;
            padding: 12px;
        }

        .upload-inline-file i {
            font-size: 24px;
            color: #8B4513;
        }

        .upload-inline-file span {
            font-size: 12px;
            word-break: break-word;
        }

        .file-preview-grid {
              display: none;
        }

        .file-preview {
            border: 1px solid #e0d5c7;
            border-radius: 8px;
            padding: 8px;
            background: #fafaf9;
            position: relative;
        }

        .file-preview img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }

        .checkbox-group {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            margin-top: 12px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-top: 4px;
            min-width: 20px;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .checkbox-group label {
            font-size: 13px;
            font-weight: 500;
            text-transform: none;
            letter-spacing: normal;
            cursor: pointer;
            flex: 1;
        }

        .checkbox-group a {
            color: #b96c58;
            text-decoration: none;
        }

        .checkbox-group a:hover {
            text-decoration: underline;
        }

        .submit-btn {
            margin-top: 20px;
            border: none;
            border-radius: 10px;
            padding: 12px 16px;
            background: #8B4513;
            color: white;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover:not(:disabled) {
            background: #6f5d52;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(139, 69, 19, 0.25);
        }

        .submit-btn:disabled {
            background: #d4a59b;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .muted {
            color: #87756a;
            font-size: 12px;
        }

        .nbi-card {
            border: 1px solid #e5d39f;
            border-radius: 16px;
            padding: 20px;
            background: linear-gradient(180deg, #fffdf8 0%, #f8f1e3 100%);
            display: grid;
            gap: 14px;
        }

        .nbi-card-header {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .nbi-card-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(139, 69, 19, 0.12);
            color: #8B4513;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        .nbi-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #3f2d22;
            margin-bottom: 4px;
        }

        .nbi-card-copy {
            font-size: 13px;
            line-height: 1.55;
            color: #6f5d52;
        }

        .nbi-help-note {
            border-left: 3px solid #d4a563;
            padding: 10px 12px;
            background: rgba(255, 255, 255, 0.75);
            border-radius: 10px;
            font-size: 13px;
            color: #6f5d52;
        }

        .nbi-action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .nbi-button,
        .nbi-guide-summary {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 13px 16px;
            text-align: center;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 48px;
        }

        .nbi-button {
            background: #8B4513;
            color: white;
        }

        .nbi-button:hover {
            background: #6f5d52;
        }

        .nbi-guide {
            border: 1px solid #e5d39f;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.8);
            overflow: hidden;
        }

        .nbi-guide summary {
            list-style: none;
        }

        .nbi-guide summary::-webkit-details-marker {
            display: none;
        }

        .nbi-guide summary::marker {
            content: '';
        }

        .nbi-guide-summary {
            background: #fff2cf;
            color: #6f5d52;
            border-bottom: 1px solid #e5d39f;
        }

        .nbi-guide[open] .nbi-guide-summary {
            background: #f6e1a7;
        }

        .nbi-guide-body {
            padding: 14px;
        }

        .nbi-step-list {
            list-style: none;
            display: grid;
            gap: 10px;
        }

        .nbi-step {
            display: grid;
            grid-template-columns: 32px 1fr;
            gap: 10px;
            align-items: flex-start;
            padding: 12px;
            border-radius: 12px;
            background: #fffaf3;
            border: 1px solid #eadfc9;
        }

        .nbi-step-number {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: #8B4513;
            color: white;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 13px;
        }

        .nbi-step-title {
            font-weight: 700;
            color: #3f2d22;
            margin-bottom: 2px;
            font-size: 14px;
        }

        .nbi-step-copy {
            color: #6f5d52;
            font-size: 13px;
            line-height: 1.5;
        }

        .field-note {
            font-size: 12px;
            color: #87756a;
            line-height: 1.4;
        }

        #touristIdentitySection:not(.hidden),
        #tourGuideSection:not(.hidden) {
            margin-top: 8px;
            padding-top: 6px;
            display: grid;
            gap: 18px;
        }

        #tourGuideSection .form-section-title {
            margin-top: 0;
            margin-bottom: 10px;
        }

        #tourGuideSection .grid-2 {
            margin-bottom: 2px;
        }

        #tourGuideSection .nbi-card {
            margin: 4px 0;
        }

        #tourGuideSection .checkbox-group {
            margin-top: 4px;
            margin-bottom: 2px;
        }

        @media (max-width: 1024px) {
            body {
                align-items: flex-start;
                padding-top: 74px;
            }

            .container {
                margin: 0 auto;
                max-height: calc(100vh - 86px);
            }
        }

        @media (max-width: 700px) {
            body {
                padding: 16px 12px;
            }

            .container {
                padding: 26px 18px;
                max-width: 100%;
                margin: 14px 0;
                max-height: calc(100vh - 28px);
            }

            h1 {
                font-size: 22px;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }

            .brand-header {
                top: 12px;
                left: 12px;
            }

            .brand-icon {
                width: 36px;
                height: 36px;
            }

            .brand-text {
                font-size: 16px;
            }

            .verification-group {
                flex-direction: column;
                align-items: stretch;
            }

            .verify-btn {
                width: 100%;
                min-height: 44px;
            }

            .nbi-card {
                padding: 16px;
            }

            #tourGuideSection {
                gap: 14px;
                margin-top: 4px;
            }

            .nbi-step {
                grid-template-columns: 28px 1fr;
                padding: 10px;
            }

            .nbi-step-number {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .nbi-button,
            .nbi-guide-summary {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 12px 10px;
                padding-top: 64px;
            }

            .container {
                padding: 20px 14px;
                border-radius: 14px;
                max-height: calc(100vh - 74px);
            }

            .brand-header {
                top: 8px;
                left: 8px;
            }

            .brand-icon {
                width: 32px;
                height: 32px;
                border-radius: 8px;
            }

            .brand-text {
                display: block;
                font-size: 13px;
                max-width: 112px;
                line-height: 1.1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .role-toggle {
                grid-template-columns: 1fr;
            }

            .role-btn {
                padding: 12px;
            }

            h1 {
                font-size: 20px;
            }

            .subtitle {
                margin-bottom: 20px;
            }
        }

        @media (max-height: 740px) {
            body {
                align-items: flex-start;
            }

            .container {
                max-height: calc(100vh - 22px);
                margin: 8px 0;
            }
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<div class="brand-header">
    <img src="/images/tribaltours_icon.png" alt="TrblTours" class="brand-icon">
    <div class="brand-text brand-font">TrblTours</div>
</div>

<div class="container">
    <a class="back-link" href="/">← Back to Home</a>
    <h1>Create Your Account</h1>
    <p class="subtitle">Join TrblTours with secure identity verification.</p>

    <?php if ($errors !== []) { ?>
        <div class="alert error">
            <strong>Please fix the following:</strong>
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <?php if ($successMessage !== '') { ?>
        <div class="alert success">
            <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?><br>
            Redirecting to login in 3 seconds...
        </div>
    <?php } ?>

    <div class="role-toggle">
        <button type="button" class="role-btn" id="touristTab">
            <span class="role-icon"><i class="fas fa-user"></i></span>
            <span>Tourist</span>
        </button>
        <button type="button" class="role-btn" id="guideTab">
            <span class="role-icon"><i class="fas fa-map"></i></span>
            <span>Tour Guide</span>
        </button>
    </div>

    <?php if ($requestedRole === 'tour_guide') { ?>
        <div class="info-banner">
            <i class="fas fa-info-circle"></i>
            <span><strong>Approval Required:</strong> Account will be pending until admin approves your identity documents. This takes 2-3 business days.</span>
        </div>
    <?php } ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="account_type" id="account_type" value="<?= oldValue($formData, 'account_type') ?>">

        <!-- Account Information -->
        <div class="form-section-title">Account Information</div>

        <div class="grid-2">
            <div class="field">
                <label for="full_name" class="required">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="John Doe" value="<?= oldValue($formData, 'full_name') ?>" required>
            </div>

            <div class="field">
                <label for="date_of_birth" class="required">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?= oldValue($formData, 'date_of_birth') ?>" required>
            </div>
        </div>

        <!-- Passwords -->
        <div class="grid-2">
            <div class="field">
                <label for="password" class="required">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="field">
                <label for="password_confirmation" class="required">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
            </div>
        </div>

        <!-- Contact Verification -->
        <div class="form-section-title">Contact Verification</div>

        <div class="field">
            <label for="email" class="required">Email</label>
            <div class="verification-group">
                <input type="email" id="email" name="email" placeholder="john@example.com" value="<?= oldValue($formData, 'email') ?>" required>
                <button type="button" class="verify-btn" id="emailVerifyBtn">Verify Email</button>
            </div>
        </div>

        <div id="emailOtpField" class="field hidden">
            <label for="email_otp">Email OTP</label>
            <div class="verification-group">
                <input type="text" id="email_otp" class="otp-input" placeholder="000000" maxlength="6">
                <button type="button" class="verify-btn" id="emailConfirmBtn">Confirm OTP</button>
            </div>
            <input type="hidden" name="email_verified" id="email_verified_input" value="0">
            <p class="muted" id="emailVerifiedStatus"></p>
        </div>

        <div class="field">
            <label for="phone_number" class="required">Phone Number</label>
            <div class="verification-group">
                <input type="tel" id="phone_number" name="phone_number" placeholder="+63 999-999-9999" value="<?= oldValue($formData, 'phone_number') ?>" required>
                <button type="button" class="verify-btn" id="phoneVerifyBtn">Send OTP</button>
            </div>
        </div>

        <div id="phoneOtpField" class="field hidden">
            <label for="phone_otp">Phone OTP</label>
            <div class="verification-group">
                <input type="text" id="phone_otp" class="otp-input" placeholder="000000" maxlength="6">
                <button type="button" class="verify-btn" id="phoneConfirmBtn">Confirm OTP</button>
            </div>
            <input type="hidden" name="phone_verified" id="phone_verified_input" value="0">
            <p class="muted" id="phoneVerifiedStatus"></p>
        </div>

        <!-- Personal Information -->
        <div class="form-section-title">Personal Information</div>

        <div class="field">
            <label for="nationality" class="required">Nationality</label>
            <select id="nationality" name="nationality" required>
                <?php foreach ($countryOptions as $country) { ?>
                    <option value="<?= htmlspecialchars($country, ENT_QUOTES, 'UTF-8') ?>" <?= selected((string) $formData['nationality'], (string) $country) ?>>
                        <?= htmlspecialchars($country, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- Tourist Identity Verification -->
        <div id="touristIdentitySection">
            <div class="form-section-title">Identity Verification</div>

            <div class="field">
                <label for="tourist_id_type" class="required">Valid ID Type</label>
                <select id="tourist_id_type" name="tourist_id_type" required>
                    <?php foreach ($touristIdOptions as $touristIdValue => $touristIdLabel) { ?>
                        <option value="<?= htmlspecialchars($touristIdValue, ENT_QUOTES, 'UTF-8') ?>" <?= selected((string) $formData['tourist_id_type'], (string) $touristIdValue) ?>>
                            <?= htmlspecialchars($touristIdLabel, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="field">
                <label for="tourist_id_number">ID Number (Optional)</label>
                <input type="text" id="tourist_id_number" name="tourist_id_number" placeholder="e.g., AB1234567" value="<?= oldValue($formData, 'tourist_id_number') ?>">
            </div>

            <div class="field">
                <label class="required">Upload Your Valid ID (Front)</label>
                <div class="file-upload-wrapper">
                    <label for="tourist_id_front_file" class="file-upload-label">
                        <i class="fas fa-image"></i>
                        <span>Click to upload</span>
                        <span class="muted">JPG, PNG, or PDF (max 5MB)</span>
                    </label>
                    <input type="file" id="tourist_id_front_file" name="tourist_id_front_file" accept="image/*,.pdf">
                </div>
                <div class="file-preview-grid" id="tourist_id_front_preview"></div>
            </div>

            <div class="field">
                <label class="required">Upload Your Valid ID (Back)</label>
                <div class="file-upload-wrapper">
                    <label for="tourist_id_back_file" class="file-upload-label">
                        <i class="fas fa-image"></i>
                        <span>Click to upload</span>
                        <span class="muted">JPG, PNG, or PDF (max 5MB)</span>
                    </label>
                    <input type="file" id="tourist_id_back_file" name="tourist_id_back_file" accept="image/*,.pdf">
                </div>
                <div class="file-preview-grid" id="tourist_id_back_preview"></div>
            </div>

            <p class="field-note">We use your ID to verify your identity and prevent fake accounts.</p>
        </div>

        <!-- Tour Guide Section -->
        <div id="tourGuideSection" class="hidden">
            <div class="form-section-title">Tour Guide Credentials</div>

            <div class="grid-2">
                <div class="field">
                    <label for="government_id_type" class="required">Government ID Type</label>
                    <select id="government_id_type" name="government_id_type" required>
                        <option value="national_id" <?= selected($formData['government_id_type'], 'national_id') ?>>National ID</option>
                        <option value="passport" <?= selected($formData['government_id_type'], 'passport') ?>>Passport</option>
                        <option value="drivers_license" <?= selected($formData['government_id_type'], 'drivers_license') ?>>Driver's License</option>
                        <option value="other" <?= selected($formData['government_id_type'], 'other') ?>>Other</option>
                    </select>
                </div>

                <div class="field">
                    <label for="government_id_number" class="required">Government ID Number</label>
                    <input type="text" id="government_id_number" name="government_id_number" placeholder="e.g., 123-456-789-000" value="<?= oldValue($formData, 'government_id_number') ?>">
                </div>
            </div>

            <div class="field">
                <label class="required">Upload Government ID (Front)</label>
                <div class="file-upload-wrapper">
                    <label for="id_front_file" class="file-upload-label">
                        <i class="fas fa-image"></i>
                        <span>Click to upload</span>
                        <span class="muted">JPG, PNG, or PDF (max 5MB)</span>
                    </label>
                    <input type="file" id="id_front_file" name="id_front_file" accept="image/*,.pdf">
                </div>
                <div class="file-preview-grid" id="id_front_preview"></div>
            </div>

            <div class="field">
                <label class="required">Upload Government ID (Back)</label>
                <div class="file-upload-wrapper">
                    <label for="id_back_file" class="file-upload-label">
                        <i class="fas fa-image"></i>
                        <span>Click to upload</span>
                        <span class="muted">JPG, PNG, or PDF (max 5MB)</span>
                    </label>
                    <input type="file" id="id_back_file" name="id_back_file" accept="image/*,.pdf">
                </div>
                <div class="file-preview-grid" id="id_back_preview"></div>
            </div>

            <div class="field">
                <label class="required">Upload Selfie Holding Your ID</label>
                <div class="file-upload-wrapper">
                    <label for="selfie_file" class="file-upload-label">
                        <i class="fas fa-user"></i>
                        <span>Click to upload or take a photo</span>
                        <span class="muted">JPG or PNG (max 5MB)</span>
                    </label>
                    <input type="file" id="selfie_file" name="selfie_file" accept="image/*">
                </div>
                <div class="file-preview-grid" id="selfie_preview"></div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label for="years_of_experience" class="required">Years of Experience</label>
                    <input type="number" min="0" id="years_of_experience" name="years_of_experience" placeholder="e.g., 5">
                </div>

                <div class="field">
                    <label for="bio" class="required">Bio / About You</label>
                    <textarea id="bio" name="bio" placeholder="Tell us about your experience..."></textarea>
                </div>
            </div>

            <div class="nbi-card">
                <div class="nbi-card-header">
                    <div class="nbi-card-icon"><i class="fas fa-id-card"></i></div>
                    <div>
                        <div class="nbi-card-title">NBI Clearance Requirement</div>
                        <p class="nbi-card-copy">
                            Tour guides must submit a valid NBI Clearance for identity review.
                            You can apply or track your clearance at
                            <a href="https://clearance.nbi.gov.ph" target="_blank" rel="noopener noreferrer">clearance.nbi.gov.ph</a>.
                        </p>
                    </div>
                </div>

                <div class="nbi-help-note">
                    If your clearance is still being processed, you can continue filling out the form now and submit once the document is ready.
                </div>

                <div class="nbi-action-row">
                    <a class="nbi-button" href="https://clearance.nbi.gov.ph" target="_blank" rel="noopener noreferrer">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                        Go to NBI Website
                    </a>
                </div>

                <details class="nbi-guide">
                    <summary class="nbi-guide-summary">Click here for step-by-step guide</summary>
                    <div class="nbi-guide-body">
                        <ol class="nbi-step-list">
                            <li class="nbi-step">
                                <span class="nbi-step-number">1</span>
                                <div>
                                    <div class="nbi-step-title">Create or sign in to your NBI account</div>
                                    <div class="nbi-step-copy">Open the NBI website and log in, or create a new profile if you do not have one yet.</div>
                                </div>
                            </li>
                            <li class="nbi-step">
                                <span class="nbi-step-number">2</span>
                                <div>
                                    <div class="nbi-step-title">Fill out your application details</div>
                                    <div class="nbi-step-copy">Enter your personal information carefully and review every field before continuing.</div>
                                </div>
                            </li>
                            <li class="nbi-step">
                                <span class="nbi-step-number">3</span>
                                <div>
                                    <div class="nbi-step-title">Choose a schedule and payment option</div>
                                    <div class="nbi-step-copy">Select your appointment date, then pay the required fee using the available payment channels.</div>
                                </div>
                            </li>
                            <li class="nbi-step">
                                <span class="nbi-step-number">4</span>
                                <div>
                                    <div class="nbi-step-title">Visit the clearance center</div>
                                    <div class="nbi-step-copy">Bring your reference number and valid IDs to your appointment for photo, fingerprint, and biometric capture.</div>
                                </div>
                            </li>
                            <li class="nbi-step">
                                <span class="nbi-step-number">5</span>
                                <div>
                                    <div class="nbi-step-title">Upload the issued clearance here</div>
                                    <div class="nbi-step-copy">Once your clearance is released, upload the document below and confirm that it is valid.</div>
                                </div>
                            </li>
                        </ol>
                    </div>
                </details>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label for="nbi_clearance_number" class="required">NBI Clearance Number</label>
                    <input type="text" id="nbi_clearance_number" name="nbi_clearance_number" placeholder="e.g., NBI-1234-5678" value="<?= oldValue($formData, 'nbi_clearance_number') ?>">
                    <p class="field-note">Enter the reference or clearance number shown on your NBI document.</p>
                </div>

                <div class="field">
                    <label for="barangay_clearance_number">Barangay Clearance Number (Optional)</label>
                    <input type="text" id="barangay_clearance_number" name="barangay_clearance_number" placeholder="e.g., BRGY-2026-001" value="<?= oldValue($formData, 'barangay_clearance_number') ?>">
                </div>
            </div>

            <div class="field">
                <label class="required">Upload NBI Clearance</label>
                <div class="file-upload-wrapper">
                    <label for="nbi_clearance_file" class="file-upload-label">
                        <i class="fas fa-file-upload"></i>
                        <span>Tap to upload your NBI Clearance</span>
                        <span class="muted">JPG, PNG, or PDF</span>
                    </label>
                    <input type="file" id="nbi_clearance_file" name="nbi_clearance_file" accept="image/*,.pdf">
                </div>
                <div class="file-preview-grid" id="nbi_clearance_preview"></div>
            </div>

            <div class="field">
                <label>Upload Barangay Clearance (Optional)</label>
                <div class="file-upload-wrapper">
                    <label for="barangay_clearance_file" class="file-upload-label">
                        <i class="fas fa-file-contract"></i>
                        <span>Tap to upload Barangay Clearance</span>
                        <span class="muted">JPG, PNG, or PDF</span>
                    </label>
                    <input type="file" id="barangay_clearance_file" name="barangay_clearance_file" accept="image/*,.pdf">
                </div>
                <div class="file-preview-grid" id="barangay_clearance_preview"></div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="nbi_clearance_valid" name="nbi_clearance_valid" value="1">
                <label for="nbi_clearance_valid">I confirm my NBI Clearance is valid</label>
            </div>
        </div>

        <!-- Agreements -->
        <div class="form-section-title">Agreements & Consent</div>

        <div class="checkbox-group">
            <input type="checkbox" id="terms_agreed" name="terms_agreed" value="1" required>
            <label for="terms_agreed">I agree to the <a href="#" target="_blank">Terms & Conditions</a> and <a href="#" target="_blank">Privacy Policy</a></label>
        </div>

        <div id="identityConsentCheckbox" class="checkbox-group hidden">
            <input type="checkbox" id="identity_consent" name="identity_consent" value="1">
            <label for="identity_consent">I consent to identity verification and background check</label>
        </div>

        <div id="pendingUnderstandedCheckbox" class="checkbox-group hidden">
            <input type="checkbox" id="pending_understood" name="pending_understood" value="1">
            <label for="pending_understood">I understand that my account will be pending for 2-3 business days while my NBI Clearance and identity documents are reviewed</label>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn" disabled>Register</button>
    </form>
</div>

<script>
    const touristTab = document.getElementById('touristTab');
    const guideTab = document.getElementById('guideTab');
    const accountTypeInput = document.getElementById('account_type');
    const touristIdentitySection = document.getElementById('touristIdentitySection');
    const tourGuideSection = document.getElementById('tourGuideSection');
    const identityConsentCheckbox = document.getElementById('identityConsentCheckbox');
    const pendingUnderstandedCheckbox = document.getElementById('pendingUnderstandedCheckbox');
    const submitBtn = document.getElementById('submitBtn');

    // Email verification
    const emailVerifyBtn = document.getElementById('emailVerifyBtn');
    const emailOtpField = document.getElementById('emailOtpField');
    const emailVerifiedInput = document.getElementById('email_verified_input');
    const emailVerifiedStatus = document.getElementById('emailVerifiedStatus');
    const emailConfirmBtn = document.getElementById('emailConfirmBtn');
    const emailOtpInput = document.getElementById('email_otp');

    // Phone verification
    const phoneVerifyBtn = document.getElementById('phoneVerifyBtn');
    const phoneOtpField = document.getElementById('phoneOtpField');
    const phoneVerifiedInput = document.getElementById('phone_verified_input');
    const phoneVerifiedStatus = document.getElementById('phoneVerifiedStatus');
    const phoneConfirmBtn = document.getElementById('phoneConfirmBtn');
    const phoneOtpInput = document.getElementById('phone_otp');

    let emailVerified = false;
    let phoneVerified = false;
    let emailOtp = '';
    let phoneOtp = '';

    function toggleRole(role) {
        accountTypeInput.value = role;

        if (role === 'tourist') {
            touristTab.classList.add('active');
            guideTab.classList.remove('active');
            touristIdentitySection.classList.remove('hidden');
            tourGuideSection.classList.add('hidden');
            identityConsentCheckbox.classList.add('hidden');
            pendingUnderstandedCheckbox.classList.add('hidden');
        } else {
            guideTab.classList.add('active');
            touristTab.classList.remove('active');
            touristIdentitySection.classList.add('hidden');
            tourGuideSection.classList.remove('hidden');
            identityConsentCheckbox.classList.remove('hidden');
            pendingUnderstandedCheckbox.classList.remove('hidden');
        }

        updateSubmitButtonState();
    }

    touristTab.addEventListener('click', () => toggleRole('tourist'));
    guideTab.addEventListener('click', () => toggleRole('tour_guide'));

    // Email verification
    emailVerifyBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value;
        if (email && email.includes('@')) {
            emailOtp = String(Math.floor(100000 + Math.random() * 900000));
            console.log('Email OTP (for testing):', emailOtp);
            alert(`OTP sent to ${email}\n(Testing: OTP is ${emailOtp})`);
            emailOtpField.classList.remove('hidden');
        } else {
            alert('Please enter a valid email first');
        }
    });

    emailConfirmBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (emailOtpInput.value === emailOtp) {
            emailVerified = true;
            emailVerifiedInput.value = '1';
            emailVerifiedStatus.textContent = '✓ Email verified';
            emailVerifiedStatus.style.color = '#059669';
            emailOtpField.classList.add('hidden');
            updateSubmitButtonState();
        } else {
            alert('Invalid OTP. Please try again.');
        }
    });

    // Phone verification
    phoneVerifyBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const phone = document.getElementById('phone_number').value;
        if (phone) {
            phoneOtp = String(Math.floor(100000 + Math.random() * 900000));
            console.log('Phone OTP (for testing):', phoneOtp);
            alert(`OTP sent to ${phone}\n(Testing: OTP is ${phoneOtp})`);
            phoneOtpField.classList.remove('hidden');
        } else {
            alert('Please enter a phone number first');
        }
    });

    phoneConfirmBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (phoneOtpInput.value === phoneOtp) {
            phoneVerified = true;
            phoneVerifiedInput.value = '1';
            phoneVerifiedStatus.textContent = '✓ Phone verified';
            phoneVerifiedStatus.style.color = '#059669';
            phoneOtpField.classList.add('hidden');
            updateSubmitButtonState();
        } else {
            alert('Invalid OTP. Please try again.');
        }
    });

    // File preview handlers
    function setupFileUploadPreview(inputId, previewContainerId) {
        const input = document.getElementById(inputId);
        const previewContainer = document.getElementById(previewContainerId);
        const uploadWrapper = input.closest('.file-upload-wrapper');
        const uploadLabel = uploadWrapper ? uploadWrapper.querySelector('.file-upload-label') : null;

        function resetInlinePreview() {
            if (!uploadWrapper || !uploadLabel) {
                return;
            }

            uploadWrapper.classList.remove('has-preview');
            const inlinePreview = uploadLabel.querySelector('.upload-inline-preview');

            if (inlinePreview) {
                inlinePreview.remove();
            }

            const inlineFile = uploadLabel.querySelector('.upload-inline-file');

            if (inlineFile) {
                inlineFile.remove();
            }
        }

        input.addEventListener('change', (e) => {
            previewContainer.innerHTML = '';
            resetInlinePreview();
            const file = e.target.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    if (uploadWrapper && uploadLabel) {
                        const inlineImage = document.createElement('img');
                        inlineImage.className = 'upload-inline-preview';
                        inlineImage.src = event.target.result;
                        inlineImage.alt = 'Selected file preview';
                        uploadLabel.appendChild(inlineImage);
                        uploadWrapper.classList.add('has-preview');
                    }
                };
                reader.readAsDataURL(file);
            } else if (file) {
                if (uploadWrapper && uploadLabel) {
                    const inlineFile = document.createElement('div');
                    inlineFile.className = 'upload-inline-file';

                    const inlineIcon = document.createElement('i');
                    inlineIcon.className = 'fas fa-file-lines';

                    const inlineName = document.createElement('span');
                    inlineName.textContent = file.name;

                    inlineFile.appendChild(inlineIcon);
                    inlineFile.appendChild(inlineName);
                    uploadLabel.appendChild(inlineFile);
                    uploadWrapper.classList.add('has-preview');
                }
            }
            updateSubmitButtonState();
        });
    }

    setupFileUploadPreview('id_front_file', 'id_front_preview');
    setupFileUploadPreview('id_back_file', 'id_back_preview');
    setupFileUploadPreview('selfie_file', 'selfie_preview');
    setupFileUploadPreview('tourist_id_front_file', 'tourist_id_front_preview');
    setupFileUploadPreview('tourist_id_back_file', 'tourist_id_back_preview');
    setupFileUploadPreview('nbi_clearance_file', 'nbi_clearance_preview');
    setupFileUploadPreview('barangay_clearance_file', 'barangay_clearance_preview');

    function initPasswordToggles() {
        if (!document.getElementById('password-toggle-style')) {
            const style = document.createElement('style');
            style.id = 'password-toggle-style';
            style.textContent = `
                .password-toggle-wrapper {
                    position: relative;
                    width: 100%;
                }

                .password-toggle-button {
                    position: absolute;
                    right: 10px;
                    top: 50%;
                    transform: translateY(-50%);
                    border: none;
                    background: transparent;
                    color: #6f5d52;
                    font-size: 12px;
                    font-weight: 700;
                    cursor: pointer;
                    padding: 4px 6px;
                    line-height: 1;
                }

                .password-toggle-button:hover {
                    color: #3f2d22;
                }

                .password-toggle-button:focus-visible {
                    outline: 2px solid #8b4513;
                    outline-offset: 1px;
                    border-radius: 4px;
                }
            `;
            document.head.appendChild(style);
        }

        const passwordInputs = document.querySelectorAll('input[type="password"]:not([data-password-toggle])');

        passwordInputs.forEach((input) => {
            if (!(input instanceof HTMLInputElement) || input.disabled) {
                return;
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'password-toggle-wrapper';

            const parent = input.parentNode;
            if (!parent) {
                return;
            }

            parent.insertBefore(wrapper, input);
            wrapper.appendChild(input);

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'password-toggle-button';
            button.textContent = 'Show';
            button.setAttribute('aria-label', 'Show password');

            input.style.paddingRight = '72px';
            input.dataset.passwordToggle = 'true';

            button.addEventListener('click', () => {
                const shouldShowPassword = input.type === 'password';
                input.type = shouldShowPassword ? 'text' : 'password';
                button.textContent = shouldShowPassword ? 'Hide' : 'Show';
                button.setAttribute('aria-label', shouldShowPassword ? 'Hide password' : 'Show password');
            });

            wrapper.appendChild(button);
        });
    }

    initPasswordToggles();

    function updateSubmitButtonState() {
        const fullName = document.getElementById('full_name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone_number').value.trim();
        const dob = document.getElementById('date_of_birth').value;
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;
        const nationality = document.getElementById('nationality').value.trim();
        const touristIdType = document.getElementById('tourist_id_type').value;
        const touristIdFront = document.getElementById('tourist_id_front_file').files.length;
        const touristIdBack = document.getElementById('tourist_id_back_file').files.length;
        const govIdNumber = document.getElementById('government_id_number').value.trim();
        const idFront = document.getElementById('id_front_file').files.length;
        const idBack = document.getElementById('id_back_file').files.length;
        const selfie = document.getElementById('selfie_file').files.length;
        const nbiClearanceNumber = document.getElementById('nbi_clearance_number').value.trim();
        const nbiClearanceFile = document.getElementById('nbi_clearance_file').files.length;
        const nbiClearanceValid = document.getElementById('nbi_clearance_valid').checked;
        const termsAgreed = document.getElementById('terms_agreed').checked;

        const isTourGuide = accountTypeInput.value === 'tour_guide';
        const yearsExp = isTourGuide ? document.getElementById('years_of_experience').value : '1';
        const bio = isTourGuide ? document.getElementById('bio').value.trim() : 'x';
        const identityConsent = isTourGuide ? document.getElementById('identity_consent').checked : true;
        const pendingUnderstood = isTourGuide ? document.getElementById('pending_understood').checked : true;

        const basicFieldsFilled = fullName && email && phone && dob && password && passwordConfirm && nationality && termsAgreed;
        const touristFieldsFilled = isTourGuide ? true : (touristIdType && touristIdFront && touristIdBack);
        const guideFieldsFilled = !isTourGuide || (yearsExp && bio && nbiClearanceNumber && nbiClearanceFile && nbiClearanceValid && identityConsent && pendingUnderstood);
        const guideIdentityFilled = !isTourGuide || (govIdNumber && idFront && idBack && selfie);
        const allVerified = emailVerified && phoneVerified;

        submitBtn.disabled = !(basicFieldsFilled && touristFieldsFilled && guideIdentityFilled && guideFieldsFilled && allVerified);
    }

    document.getElementById('full_name').addEventListener('input', updateSubmitButtonState);
    document.getElementById('email').addEventListener('input', updateSubmitButtonState);
    document.getElementById('password').addEventListener('input', updateSubmitButtonState);
    document.getElementById('password_confirmation').addEventListener('input', updateSubmitButtonState);
    document.getElementById('phone_number').addEventListener('input', updateSubmitButtonState);
    document.getElementById('date_of_birth').addEventListener('change', updateSubmitButtonState);
    document.getElementById('nationality').addEventListener('change', updateSubmitButtonState);
    document.getElementById('tourist_id_type').addEventListener('change', updateSubmitButtonState);
    document.getElementById('tourist_id_number').addEventListener('input', updateSubmitButtonState);
    document.getElementById('government_id_number').addEventListener('input', updateSubmitButtonState);
    document.getElementById('years_of_experience').addEventListener('input', updateSubmitButtonState);
    document.getElementById('bio').addEventListener('input', updateSubmitButtonState);
    document.getElementById('nbi_clearance_number').addEventListener('input', updateSubmitButtonState);
    document.getElementById('nbi_clearance_valid').addEventListener('change', updateSubmitButtonState);
    document.getElementById('barangay_clearance_number').addEventListener('input', updateSubmitButtonState);
    document.getElementById('terms_agreed').addEventListener('change', updateSubmitButtonState);
    document.getElementById('identity_consent').addEventListener('change', updateSubmitButtonState);
    document.getElementById('pending_understood').addEventListener('change', updateSubmitButtonState);

    // Initialize
    toggleRole(accountTypeInput.value === 'tour_guide' ? 'tour_guide' : 'tourist');
    updateSubmitButtonState();
</script>
</body>
</html>
