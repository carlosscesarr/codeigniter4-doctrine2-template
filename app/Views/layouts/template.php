<!DOCTYPE html>
<html>
<head>
    <title>Meu Projeto CodeIgniter</title>
    <link rel="stylesheet" href="<?= base_url() . 'assets/bootstrap-4.6/css/bootstrap.min.css' ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body>

<?= $this->renderSection('content') ?>

<script src="<?= base_url('assets/bootstrap-4.6/js/bootstrap.bundle.min.js') ?>"></script>
<?= $this->renderSection('scripts') ?>

</body>
</html>