<?php

namespace JFStudio;

class Router {
    const Backoffice = 1;
    const Profile = 2;
    const Gains = 3;
    const Referrals = 4;
    const Signup = 5;
    const Login = 6;
    const RecoverPassword = 7;
    const NewPassword = 8;
    const Notifications = 15;
    const Plans = 16;
    const TradingView = 22;
    const Wallet = 23;
    const Calculator = 25;
    const AddFunds = 28;
    const Banner = 36;
    const AddCampaign = 37;
    const AddBanner = 38;
    const Launch = 39;
    const Landing = 40;
    const EditCampaign = 41;
    const VCard = 42;
    const AddVCard = 43;
    const EditVCard = 44;
    const StoreNetwork = 46;
    const StoreMarketing = 47;
    const Invoices = 48;
    const Academy = 49;
    const WithdrawMethods = 51;
    const GainsReport = 52;
    const WalletProcess = 53;
    const ProfileSetting = 54;
    const AcademyLesson = 55;
    const Tools = 60;
    const Home = 61;
    const Landing2 = 63;
    const PayPal = 64;
    const ZuumTools = 65;
    const QuickMoney = 66;
    const Help = 67;
    const AdminTicket = 68;
    const BlockChain = 69;
    const ViewTransaction = 70;
    const ViewAddress = 71;
    const Keys = 72;
    const Payments = 73;
    const AddPayment = 74;
    const StoreCredit = 75;
    const Iptv = 76;
    const IptvDemo = 77;
    const IptvAddClient = 78;
    const Movies = 80;
    const Game = 81;
    const GameGuide = 82;
    const ImagesBank = 83;
    const Rewards = 84;
    
    /* admin */
    const AdminUsers = 9;
    const AdminActivations = 10;
    const AdminAdministrators = 11;
    const AdminBrokers = 12;
    const AdminLogin = 13;
    const AdmiActivation = 14;
    const AdminBrokersEdit = 16;
    const AdminBrokersAdd = 17;
    const AdminBrokersCapitals = 18;
    const AdminUserEdit = 19;
    const AdminUserAdd = 20;
    const AdminAdministratorsAdd = 21;
    const AdminAdministratorsEdit = 21;
    const AdminTransactions = 24;
    const AdminDash = 26;
    const AdminAddOldComissions = 27;
    const AdminDeposits = 29;
    const AdminTransactionsList = 30;
    const AdminNotices = 31;
    const AdminNoticesEdit = 32;
    const AdminNoticesAdd = 33;
    const AdminStats = 34;
    const AdminReport = 35;
    const AdminTemplates = 45;
    const AdminBuys = 50;
    const AdminWallet = 56;
    const AdminTools = 57;
    const AdminToolsAdd = 58;
    const AdminToolsEdit = 59;
    const AdminEmail = 62;
    const AdminIPTV = 79;
    const AdminLanding = 85;
    static function getName(int $route = null)
    {
        switch ($route) 
        {
            case self::Backoffice:
                return 'Oficina virtual';
            case self::Profile:
                return 'Perfil';
            case self::Gains:
                return 'Ganancias';
            case self::Referrals:
                return 'Mis referidos';
            case self::Signup:
                return 'Únete hoy mismo';
            case self::Login:
                return 'Ingresa a tu cuenta';
            case self::RecoverPassword:
                return 'Recuperar contraseña';
            case self::NewPassword:
                return 'Cambiar contraseña';
            case self::Plans:
                return 'Planes';
            case self::Notifications:
                return 'Notificaciones';
            case self::TradingView:
                return 'Resultados del broker';
            case self::Wallet:
                return 'Cartera electrónica';
            case self::Calculator:
                return 'Calculadora';
            case self::AddFunds:
                return 'Añadir fondos';
            case self::AdminDash:
                return 'Administrador';
            case self::AdminUsers:
                return 'Usuarios';
            case self::AdminUserEdit:
                return 'Editar usuario';
            case self::AdminUserAdd:
                return 'Añadir usuario';
            case self::AdminActivations:
                return 'Activaciones';
            case self::AdminAdministrators:
                return 'Administradores';
            case self::AdminAdministratorsAdd:
                return 'Añadir administrador';
            case self::AdminAdministratorsEdit:
                return 'Editar administrador';
            case self::AdminBrokers:
                return 'Brokers';
            case self::AdminBrokersEdit:
                return 'Editar broker';
            case self::AdminBrokersAdd:
                return 'Añadir broker';
            case self::AdminBrokersAdd:
                return 'Capital invertido';
            case self::AdminLogin:
                return 'Iniciar sesión admin';
            case self::AdmiActivation:
                return 'Activar en plan';
            case self::AdminTransactions:
                return 'Transacciones';
            case self::AdminAddOldComissions:
                return 'Añadir comisiones atrasadas';
            case self::AdminTransactionsList:
                return 'Lista de fondeos';
            case self::AdminNotices:
                return 'Listar noticias';
            case self::AdminNoticesEdit:
                return 'Editar noticia';
            case self::AdminNoticesAdd:
                return 'Añadir noticia';
            case self::AdminDeposits:
                return 'Ver fondeos';
            case self::AdminStats:
                return 'Estadísticas';
            case self::AdminReport:
                return 'Reporte';
            case self::AddCampaign:
                return 'Añadir campaña';
            case self::EditCampaign:
                return 'Editar campaña';
            case self::AddBanner:
                return 'Añadir banner';
            case self::Banner:
                return 'Banners';
            case self::Launch:
                return 'Pre Lanzamiento';
            case self::Landing:
                return 'Landing Page';
            case self::VCard:
                return 'Lista de VCards';
            case self::AddVCard:
                return 'Añadir VCard';
            case self::EditVCard:
                return 'Editar VCard';
            case self::AdminTemplates:
                return 'Templates';
            case self::StoreNetwork:
                return 'Tienda';
            case self::StoreMarketing:
                return 'Tienda de Cursos';
            case self::StoreMarketing:
                return 'Tienda de Herramientas';
            case self::Invoices:
                return 'Mis ordenes de compra';
            case self::Academy:
                return 'Educación';
            case self::WithdrawMethods:
                return 'Métodos de retiro';
            case self::AdminBuys:
                return 'Compras';
            case self::WalletProcess:
                return 'Procesar pago';
            case self::ProfileSetting:
                return 'Ajustes de cuenta';
            case self::AcademyLesson:
                return 'Cursos de academia';
            case self::AdminWallet:
                return 'Ewallet';
            case self::AdminTools:
                return 'Herramientas';
            case self::AdminToolsAdd:
                return 'Añadir herramienta';
            case self::AdminToolsEdit:
                return 'Editar herramienta';
            case self::Tools:
                return 'Material de trabajo';
            case self::Home:
                return 'Página inicial';
            case self::AdminEmail:
                return 'Email';
            case self::Landing2:
                return '¡Comienza una educación profesional!';
            case self::PayPal:
                return 'Pago seguro con PayPal';
            case self::ZuumTools:
                return 'ZuumTools';
            case self::QuickMoney:
                return 'QuickMoney';
            case self::Help:
                return 'Ayuda';
            case self::BlockChain:
                return 'BlockChain';
            case self::AdminTicket:
                return 'Tickets';
            case self::ViewTransaction:
                return 'Ver transacción';
            case self::ViewAddress:
                return 'Ver dirección publica';
            case self::Keys:
                return 'Licencias';
            case self::Payments:
                return 'Pagos realizados a tus cuentas';
            case self::AddPayment:
                return 'Añadir pago de mensualidad';
            case self::Iptv:
                return 'Mis clientes IPTV';
            case self::IptvDemo:
                return 'Solicitar demo';
            case self::IptvAddClient:
                return 'Añadir cliente IPTV';
            case self::StoreCredit:
                return 'Créditos';
            case self::Movies:
                return 'Infinity +';
            case self::AdminIPTV:
                return 'IPTV';
            case self::Game:
                return 'Juegos';
            case self::ImagesBank:
                return 'Banco de imagenes';
            case self::GameGuide:
                return 'Guía para juegos';
            case self::Rewards:
                return 'Recompensas';
            case self::AdminLanding:
                return 'Landing';
            default: 
                return 'Sin nombre';
        }
    }
}