<!DOCTYPE html>
<html>
<head>
    <title>REST PKI Samples</title>
    <?php include 'includes.php' ?>
</head>
<body>

<?php include 'menu.php' ?>

<div class="container">

    <h2>REST PKI Samples</h2>
    Choose one of the following samples:
    <ul>
        <li><a href="authentication.php">Authentication with digital certificate</a></li>
        <li>
            PAdES signature
            <ul>
                <li><a href="pades-signature.php">Create a signature with a file already on server</a></li>
                <li><a href="upload.php?goto=pades-signature">Create a signature with a file uploaded by user</a></li>
                <li><a href="upload.php?goto=open-pades-signature">Open/validate an existing signature</a></li>
                <li><a href="upload.php?goto=pades-signature-server-key">Create a signature using a server key</a></li>
            </ul>
        </li>
        <li>
            CAdES signature
            <ul>
                <li><a href="cades-signature.php">Create a signature with a file already on server</a></li>
                <li><a href="upload.php?goto=cades-signature">Create a signature with a file uploaded by user</a></li>
                <li><a href="upload.php?goto=open-cades-signature">Open/validate an existing signature</a></li>
                <li><a href="upload.php?goto=cades-signature-server-key">Create a signature using a server key</a></li>
            </ul>
        </li>
        <li>
            XML signature
            <ul>
                <li><a href="xml-full-signature.php">Create a full XML signature (enveloped signature)</a></li>
                <li><a href="xml-element-signature.php">Create a XML element signature</a></li>
                <li><a href="upload.php?goto=open-xml-signature">Open/validate signatures on an existing XML file</a></li>
            </ul>
        </li>
        <li>
            Batch signature
            <ul>
                <li><a href="../../../batch-signature.php">Simple batch of PAdES signatures</a></li>
                <li><a href="batch-xml-element-signature.php">Simple batch of XML element signatures on the same document</a></li>
            </ul>
        </li>
    </ul>

</div>
</body>
</html>
