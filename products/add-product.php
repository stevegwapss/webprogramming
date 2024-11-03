<?php

require_once('../tools/functions.php');
require_once('../classes/product-image.class.php');

$code = $name = $category = $price = $image = $imageTemp = '';
$codeErr = $nameErr = $categoryErr = $priceErr = $imageErr = '';

$uploadDir = '../uploads/';
$allowedType = ['jpg', 'jpeg', 'png'];

$productObj = new ProductImage();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $code = clean_input($_POST['code']);
    $name = clean_input($_POST['name']);
    $category = clean_input($_POST['category']);
    $price = clean_input($_POST['price']);
    $image = $_FILES['product_image']['name'];
    $imageTemp = $_FILES['product_image']['tmp_name'];

    // Validate Product Code
    if (empty($code)) {
        $codeErr = 'Product Code is required.';
    } elseif ($productObj->codeExists($code)) {
        $codeErr = 'Product Code already exists.';
    }

    // Validate Name
    if (empty($name)) {
        $nameErr = 'Name is required.';
    }

    // Validate Category
    if (empty($category)) {
        $categoryErr = 'Category is required.';
    }

    // Validate Price
    if (empty($price)) {
        $priceErr = 'Price is required.';
    } elseif (!is_numeric($price)) {
        $priceErr = 'Price should be a number.';
    } elseif ($price < 1) {
        $priceErr = 'Price must be greater than 0.';
    }

    // Validate Image
    if (!empty($image)) {
        $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        if ($_FILES['product_image']['size'] > 5 * 1024 * 1024) {
            $imageErr = 'Image must not exceed 5MB.';
        } elseif (!in_array($imageFileType, $allowedType)) {
            $imageErr = 'Accepted files are JPG, JPEG, and PNG only.';
        }
    } else {
        $imageErr = 'Product image is required.';
    }

    // If there are validation errors, return them as JSON
    if (!empty($codeErr) || !empty($nameErr) || !empty($categoryErr) || !empty($priceErr) || !empty($imageErr)) {
        echo json_encode([
            'status' => 'error',
            'codeErr' => $codeErr,
            'nameErr' => $nameErr,
            'categoryErr' => $categoryErr,
            'priceErr' => $priceErr,
            'imageErr' => $imageErr
        ]);
        exit;
    }

    // If all validations pass
    if (empty($codeErr) && empty($nameErr) && empty($categoryErr) && empty($priceErr) && empty($imageErr)) {
        $productObj->code = $code;
        $productObj->name = $name;
        $productObj->category_id = $category;
        $productObj->price = $price;

        if ($productObj->add()) {

            // Generate a unique file path for the image
            $targetImage = $uploadDir . uniqid() . '_' . basename($image);
            if (move_uploaded_file($imageTemp, $targetImage)) {
                $productObj->file_path = $targetImage;
                $productObj->image_role = 'main';
                $productObj->addImage();

                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload the image.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Something went wrong when adding the new product.']);
        }
        exit;
    }
}
?>
