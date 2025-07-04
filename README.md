MediLink – Sistema de Agendamento de Consultas Médicas

MediLink é um sistema web desenvolvido em PHP que permite o agendamento de consultas médicas online de forma simples e eficiente.  
Tanto pacientes quanto médicos possuem áreas dedicadas, com funcionalidades específicas para cada perfil.

Tela inicial do MediLink (https://cbd4b8ba-05f3-48a1-b39b-19490163c001-00-18syv4b69ag3l.spock.replit.dev/) 

Funcionalidades

- Cadastro e login de usuários (médico, paciente e administrador)
- Agendamento e cancelamento de consultas
- Gerenciamento de especialidades médicas
- Dashboard para cada tipo de usuário
- Registro e visualização de observações médicas
- Sistema com autenticação e controle de sessão
- Armazenamento em banco de dados SQLite

Tecnologias Utilizadas

- **PHP** (puro)
- **SQLite** como banco de dados
- **HTML5**, **CSS3**, **JavaScript**
- Interface com visual moderno (gradiente, responsiva)
- Replit como ambiente de execução

Estrutura de Pastas

```bash
.
├── administrador/          # Gerenciamento de médicos e especialidades
├── doutor/                 # Dashboard e observações dos médicos
├── paciente/               # Agendamentos e histórico dos pacientes
├── css/                    # Estilos visuais
├── database.db             # Banco de dados SQLite
├── index.php               # Página inicial (landing page)
├── login.php               # Tela de login
├── register.php            # Cadastro de usuários
├── logout.php              # Encerrar sessão
├── create_tabelas.php      # Criação das tabelas do BD
├── update_schema.php       # Atualização do schema SQLite
└── ...
