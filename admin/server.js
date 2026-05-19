const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');

const app = express();
const port = 3000;

app.use(express.json());
app.use(cors());

// Conexão com o banco de dados
const db = mysql.createConnection({
    host: 'localhost',
    user: 'gestaonp3benefic_regina',
    password: 'QJ6$@rAfdUbd70TG',
    database: 'gestaonp3benefic_dbgestao'
});

db.connect((err) => {
    if (err) {
        console.error('Erro ao conectar ao banco de dados:', err);
        process.exit(1);
    } else {
        console.log('Conectado ao banco de dados!');
    }
});

// Rota padrão para retornar todos os marcadores com latitude/longitude válidas
app.get('/api/marcadores', (req, res) => {
    const query = `
        SELECT p.latitude, p.longitude, pes.nome
        FROM pessoa_endereco p
        LEFT JOIN pessoa pes ON pes.id = p.pessoa_id
        WHERE p.longitude IS NOT NULL AND p.latitude IS NOT NULL
    `;

    db.query(query, (err, results) => {
        if (err) {
            console.error('Erro ao consultar o banco de dados:', err);
            return res.status(500).send('Erro ao consultar o banco de dados');
        }
        res.json(results);
    });
});

// Rota com filtro por bairros (POST)
app.post('/api/filtrar', (req, res) => {
    const filtros = req.body;
    const params = [];
    const conditions = ['p.latitude IS NOT NULL', 'p.longitude IS NOT NULL'];

    if (filtros.bairros && filtros.bairros.length > 0) {
        const placeholders = filtros.bairros.map(() => '?').join(',');
        conditions.push(`p.bairro IN (${placeholders})`);
        params.push(...filtros.bairros);
    }

    const query = `
        SELECT p.latitude, p.longitude, pes.nome
        FROM pessoa_endereco p
        LEFT JOIN pessoa pes ON pes.id = p.pessoa_id
        WHERE ${conditions.join(' AND ')}
    `;

    db.query(query, params, (err, results) => {
        if (err) {
            console.error('Erro ao consultar o banco de dados:', err);
            return res.status(500).send('Erro ao consultar o banco de dados');
        }
        res.json(results);
    });
});

// Tratamento de erros não tratados
process.on('uncaughtException', (err) => {
    console.error('Erro não tratado:', err);
});

process.on('unhandledRejection', (reason, promise) => {
    console.error('Rejeição não tratada em uma Promise:', promise, 'Razão:', reason);
});

// Servir arquivos estáticos da pasta /public (se quiser usar no front-end)
app.use(express.static('public'));

// Iniciar o servidor
app.listen(port, () => {
    console.log(`Servidor rodando em http://localhost:${port}`);
});
