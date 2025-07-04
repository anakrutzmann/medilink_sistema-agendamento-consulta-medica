<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'patient') {
    header('Location: ../login.php');
    exit;
}

require_once '../connection/connection_sqlite.php';

// Pegar especialidades
$specialties = $database->query("SELECT * FROM specialties")->fetchAll(PDO::FETCH_ASSOC);

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialty_id = $_POST['specialty'] ?? null;
    $doctor_id = $_POST['doctor'] ?? null;
    $date = $_POST['date'] ?? null;
    $time = $_POST['time'] ?? null;

    if ($specialty_id && $doctor_id && $date && $time) {
        // Pega id do paciente pelo email da sessão
        $stmt = $database->prepare("SELECT id FROM patient WHERE email = ?");
        $stmt->execute([$_SESSION['email']]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient) {
            // Insere agendamento
            $stmt = $database->prepare("
                INSERT INTO appointment (patient_id, doctor_id, date, time, status) 
                VALUES (?, ?, ?, ?, 'agendado')
            ");
            $stmt->execute([$patient['id'], $doctor_id, $date, $time]);
            $mensagem = "Consulta agendada com sucesso para $date às $time!";
        } else {
            $mensagem = "Paciente não encontrado.";
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<title>Agendar Consulta - MediLink</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: #fff;
        margin: 0;
        padding: 40px 20px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    form {
        background: rgba(255, 255, 255, 0.15);
        padding: 30px 40px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        width: 100%;
        max-width: 500px;
        color: #fff;
        position: relative;
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
        text-shadow: 2px 2px 5px rgba(0,0,0,0.4);
    }

    label {
        display: block;
        margin-top: 20px;
        font-weight: 600;
    }

    select, input[type=date] {
        width: 100%;
        padding: 10px 12px;
        margin-top: 8px;
        border: none;
        border-radius: 8px;
        font-size: 1em;
        outline: none;
        color: #333;
    }

    select option {
        color: #333;
    }

    button, .btn-link {
        margin-top: 20px;
        width: 48%;
        background: #fff;
        color: #2575fc;
        font-weight: 700;
        padding: 14px 0;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        box-shadow: 0 7px 18px rgba(0,0,0,0.25);
        transition: background 0.3s ease, color 0.3s ease;
        font-size: 1.1em;
        display: inline-block;
        text-align: center;
        text-decoration: none;
    }

    button:hover, .btn-link:hover {
        background: #2575fc;
        color: #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    }

    .message {
        margin-top: 20px;
        font-weight: 700;
        text-align: center;
        color: #cfffcf;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }

    .btn-container {
        display: flex;
        justify-content: space-between;
        margin-top: 25px;
    }
</style>

<script>
async function fetchDoctors(specialtyId) {
    if (!specialtyId) {
        document.getElementById('doctor').innerHTML = '<option value="">Selecione um médico</option>';
        document.getElementById('date').value = '';
        document.getElementById('time').innerHTML = '<option value="">Selecione um horário</option>';
        return;
    }

    const response = await fetch(`../get_doctors.php?specialty_id=${specialtyId}`);
    const doctors = await response.json();

    const doctorSelect = document.getElementById('doctor');
    doctorSelect.innerHTML = '<option value="">Selecione um médico</option>';
    doctors.forEach(doc => {
        doctorSelect.innerHTML += `<option value="${doc.id}">${doc.name}</option>`;
    });

    document.getElementById('date').value = '';
    document.getElementById('time').innerHTML = '<option value="">Selecione um horário</option>';
}

async function fetchTimes(doctorId, date) {
    if (!doctorId || !date) {
        document.getElementById('time').innerHTML = '<option value="">Selecione um horário</option>';
        return;
    }

    const response = await fetch(`../get_times.php?doctor_id=${doctorId}&date=${date}`);
    const times = await response.json();

    const timeSelect = document.getElementById('time');
    timeSelect.innerHTML = '<option value="">Selecione um horário</option>';
    times.forEach(t => {
        timeSelect.innerHTML += `<option value="${t}">${t}</option>`;
    });
}
</script>
</head>
<body>

<form method="POST">
    <h2>Agendar Consulta</h2>

    <?php if ($mensagem): ?>
        <p class="message"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <label for="specialty">Especialidade:</label>
    <select id="specialty" name="specialty" required onchange="fetchDoctors(this.value)">
        <option value="">Selecione uma especialidade</option>
        <?php foreach ($specialties as $sp): ?>
            <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="doctor">Médico:</label>
    <select id="doctor" name="doctor" required onchange="document.getElementById('date').value=''; document.getElementById('time').innerHTML='<option value=\'\'>Selecione um horário</option>'">
        <option value="">Selecione um médico</option>
    </select>

    <label for="date">Data:</label>
    <input type="date" id="date" name="date" required min="<?= date('Y-m-d') ?>" onchange="fetchTimes(document.getElementById('doctor').value, this.value)">

    <label for="time">Horário:</label>
    <select id="time" name="time" required>
        <option value="">Selecione um horário</option>
    </select>

    <button type="submit">Agendar</button>

    <div class="btn-container">
        <a href="../paciente/dashboard.php" class="btn-link">Voltar</a>
        <a href="../paciente/consultas_agendadas.php" class="btn-link">Ver Agendamentos</a>
    </div>
</form>

</body>
</html>
