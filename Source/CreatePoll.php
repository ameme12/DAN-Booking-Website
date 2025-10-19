<?php
$host = "mysql.cs.mcgill.ca";
$user = "users-909468";
$pass = "4rTPckXCr7qL";
$dbname = "2024fall-comp307-909468";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}
if (!$conn->query("USE test"))
{
    die("Failed to switch to database 'test': " . $conn->error);
}
$title = $_POST['title'];
$description = $_POST['description'];
$creator = $_POST['creator'];
$status = 1;
if (!empty($title) && !empty($description))
{
    $stmt = $conn->prepare("INSERT INTO Poll (description, title, status, creator) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $description, $title, $status, $creator);

    if ($stmt->execute())
    {

        $pollId = $stmt->insert_id; ;

        echo json_encode(['success' => true, 'pollId' => $pollId]);

    }
    else
    {
        echo json_encode(['success' => false, 'message' => $e->getMessage() ]);
    }
    $stmt->close();
}
else
{
    echo "Title and description are required!";
}

$conn->close();
?>
