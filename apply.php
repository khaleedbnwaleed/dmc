<?php
require_once 'config.php';

// Require student or applicant role to access this page
requireRole(['student', 'applicant']);

$errors = [];
$successMessage = '';
$formData = [
    'fullName' => '',
    'email' => '',
    'phone' => '',
    'dob' => '',
    'gender' => '',
    'address' => '',
    'previousInstitution' => '',
    'previousCourse' => '',
    'previousGpa' => '',
    'academicLevel' => '',
    'preferredStudyField' => '',
    'preferredInstitution' => '',
    'desiredCourse' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and collect form data
    foreach ($formData as $key => $value) {
        $formData[$key] = htmlspecialchars(trim($_POST[$key] ?? ''));
    }

    // Basic validation
    if (empty($formData['fullName'])) $errors['fullName'] = 'Full Name is required.';
    if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid Email is required.';
    if (empty($formData['phone'])) $errors['phone'] = 'Phone Number is required.';
    if (empty($formData['dob'])) $errors['dob'] = 'Date of Birth is required.';
    if (empty($formData['gender'])) $errors['gender'] = 'Gender is required.';
    if (empty($formData['address'])) $errors['address'] = 'Address is required.';
    if (empty($formData['previousInstitution'])) $errors['previousInstitution'] = 'Previous Institution is required.';
    if (empty($formData['previousCourse'])) $errors['previousCourse'] = 'Previous Course of Study is required.';
    if (empty($formData['previousGpa'])) $errors['previousGpa'] = 'Previous GPA/Grade is required.';
    if (empty($formData['academicLevel'])) $errors['academicLevel'] = 'Academic Level is required.';
    if (empty($formData['preferredStudyField'])) $errors['preferredStudyField'] = 'Preferred Study Field is required.';
    if (empty($formData['preferredInstitution'])) $errors['preferredInstitution'] = 'Preferred Institution is required.';
    if (empty($formData['desiredCourse'])) $errors['desiredCourse'] = 'Desired Course of Study is required.';

    // Simulate file uploads (in a real app, handle file uploads securely)
    // For this example, we'll just acknowledge their presence
    // if (isset($_FILES['transcript']) && $_FILES['transcript']['error'] === UPLOAD_ERR_OK) { /* handle upload */ }
    // if (isset($_FILES['admissionLetter']) && $_FILES['admissionLetter']['error'] === UPLOAD_ERR_OK) { /* handle upload */ }
    // if (isset($_FILES['idCard']) && $_FILES['idCard']['error'] === UPLOAD_ERR_OK) { /* handle upload */ }

    if (empty($errors)) {
        // In a real application, you would save $formData to a database
        // Example: $db->insert('applications', $formData);
        $successMessage = 'Your application has been submitted successfully! We will review it shortly.';
        // Clear form data after successful submission
        $formData = array_fill_keys(array_keys($formData), '');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Scholarship - Danmodi Students Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header (simplified for this example, you might include your actual header) -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <img src="logo.png" width="60" height="60" class="rounded-md" alt="Logo">
                    <div>
                        <h1 class="text-xl font-bold text-green-700">Student Portal</h1>
                        <p class="text-sm text-gray-600">Apply for Scholarship</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-sm text-gray-600 hover:text-green-600 transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Website
                    </a>
                    <a href="login.php" class="text-gray-600 hover:text-green-600 transition-colors">
                        <i class="fas fa-sign-in-alt mr-1"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-6 rounded-t-lg">
                <h2 class="text-2xl font-bold">Scholarship Application Form</h2>
                <p class="text-green-100">Fill out the form below to apply for a scholarship.</p>
            </div>
            <div class="p-6">
                <?php if (!empty($successMessage)): ?>
                    <div class="bg-green-100 text-green-800 p-4 rounded-md mb-6 flex items-center gap-2" role="alert" aria-live="assertive">
                        <i class="fas fa-check-circle h-5 w-5"></i>
                        <p class="text-sm font-medium"><?php echo $successMessage; ?></p>
                    </div>
                <?php elseif (!empty($errors)): ?>
                    <div class="bg-red-100 text-red-800 p-4 rounded-md mb-6" role="alert" aria-live="assertive">
                        <p class="text-sm font-medium mb-2">Please correct the following errors:</p>
                        <ul class="list-disc list-inside text-xs">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="apply.php" method="POST" class="space-y-6" enctype="multipart/form-data">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" id="fullName" name="fullName" value="<?php echo $formData['fullName']; ?>" placeholder="John Doe" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <?php if (isset($errors['fullName'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['fullName']; ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo $formData['email']; ?>" placeholder="john.doe@example.com" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <?php if (isset($errors['email'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['email']; ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo $formData['phone']; ?>" placeholder="+234 801 234 5678" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <?php if (isset($errors['phone'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['phone']; ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" id="dob" name="dob" value="<?php echo $formData['dob']; ?>" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <?php if (isset($errors['dob'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['dob']; ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select id="gender" name="gender" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                <option value="">Select Gender</option>
                                <option value="male" <?php echo ($formData['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo ($formData['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo ($formData['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                            <?php if (isset($errors['gender'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['gender']; ?></p><?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Residential Address</label>
                        <textarea id="address" name="address" placeholder="123 Main St, City, State" rows="3" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"><?php echo $formData['address']; ?></textarea>
                        <?php if (isset($errors['address'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['address']; ?></p><?php endif; ?>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4 mt-8">Academic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="previousInstitution" class="block text-sm font-medium text-gray-700 mb-1">Previous Institution</label>
                            <input type="text" id="previousInstitution" name="previousInstitution" value="<?php echo $formData['previousInstitution']; ?>" placeholder="University of XYZ" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <?php if (isset($errors['previousInstitution'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['previousInstitution']; ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label for="previousCourse" class="block text-sm font-medium text-gray-700 mb-1">Previous Course of Study</label>
                            <input type="text" id="previousCourse" name="previousCourse" value="<?php echo $formData['previousCourse']; ?>" placeholder="B.Sc. Computer Science" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <?php if (isset($errors['previousCourse'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['previousCourse']; ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label for="previousGpa" class="block text-sm font-medium text-gray-700 mb-1">Previous GPA/Grade</label>
                            <input type="text" id="previousGpa" name="previousGpa" value="<?php echo $formData['previousGpa']; ?>" placeholder="4.5/5.0 or A" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <?php if (isset($errors['previousGpa'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['previousGpa']; ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label for="academicLevel" class="block text-sm font-medium text-gray-700 mb-1">Current Academic Level</label>
                            <select id="academicLevel" name="academicLevel" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                <option value="">Select Level</option>
                                <option value="undergraduate" <?php echo ($formData['academicLevel'] === 'undergraduate') ? 'selected' : ''; ?>>Undergraduate</option>
                                <option value="postgraduate" <?php echo ($formData['academicLevel'] === 'postgraduate') ? 'selected' : ''; ?>>Postgraduate</option>
                                <option value="diploma" <?php echo ($formData['academicLevel'] === 'diploma') ? 'selected' : ''; ?>>Diploma</option>
                                <option value="other" <?php echo ($formData['academicLevel'] === 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                            <?php if (isset($errors['academicLevel'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['academicLevel']; ?></p><?php endif; ?>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4 mt-8">Scholarship Preferences</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="preferredStudyField" class="block text-sm font-medium text-gray-700 mb-1">Preferred Study Field</label>
                            <input type="text" id="preferredStudyField" name="preferredStudyField" value="<?php echo $formData['preferredStudyField']; ?>" placeholder="Engineering, Medicine, Arts, etc." required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <?php if (isset($errors['preferredStudyField'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['preferredStudyField']; ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label for="preferredInstitution" class="block text-sm font-medium text-gray-700 mb-1">Preferred Institution</label>
                            <input type="text" id="preferredInstitution" name="preferredInstitution" value="<?php echo $formData['preferredInstitution']; ?>" placeholder="Federal University, Jigawa State Poly" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <?php if (isset($errors['preferredInstitution'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['preferredInstitution']; ?></p><?php endif; ?>
                        </div>
                        <div class="md:col-span-2">
                            <label for="desiredCourse" class="block text-sm font-medium text-gray-700 mb-1">Desired Course of Study</label>
                            <input type="text" id="desiredCourse" name="desiredCourse" value="<?php echo $formData['desiredCourse']; ?>" placeholder="e.g., Civil Engineering, Law" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <?php if (isset($errors['desiredCourse'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['desiredCourse']; ?></p><?php endif; ?>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4 mt-8">
                        Supporting Documents (Uploads not functional in demo)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="transcript" class="block text-sm font-medium text-gray-700 mb-1">Academic Transcript</label>
                            <input type="file" id="transcript" name="transcript" disabled class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        </div>
                        <div>
                            <label for="admissionLetter" class="block text-sm font-medium text-gray-700 mb-1">Admission Letter</label>
                            <input type="file" id="admissionLetter" name="admissionLetter" disabled class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        </div>
                        <div>
                            <label for="idCard" class="block text-sm font-medium text-gray-700 mb-1">Valid ID Card</label>
                            <input type="file" id="idCard" name="idCard" disabled class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors font-semibold">
                        Submit Application
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
