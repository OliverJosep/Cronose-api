<?php

// CORE
require_once '../config/config.php';

// Controllers
require_once '../controllers/Language.controller.php';
require_once '../controllers/Work.controller.php';
require_once '../controllers/User.controller.php';
require_once '../controllers/Chat.controller.php';
require_once '../controllers/Achievement.controller.php';
require_once '../controllers/Category.controller.php';
require_once '../controllers/Specialization.controller.php';
require_once '../controllers/Province.controller.php';
require_once '../controllers/City.controller.php';
require_once '../controllers/Seniority.controller.php';
require_once '../controllers/Valoration.controller.php';
require_once '../controllers/WorkDemand.controller.php';
require_once '../controllers/Coin.controller.php';
require_once '../controllers/Image.controller.php';
require_once '../controllers/Token.controller.php';

// DAO
require_once '../dao/DAO.php';
new DAO();

// Logger
require_once '../utilities/Logger.php';

/** JWT */
require_once '../utilities/JWTManager.php';



/* ROUTES */
require_once '../utilities/routes.php';

?>
