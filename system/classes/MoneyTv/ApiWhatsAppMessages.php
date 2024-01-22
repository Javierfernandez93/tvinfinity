<?php

namespace MoneyTv;

class ApiWhatsAppMessages {
    const WELCOME_ARRAY = [
        "😁 Hola *{{name}}*, estamos muy felices de que te hayas unido a *MoneyTv*.\n\n👉 Comienza a aprender como vender tu producto en el siguiente link:\n👉zuum.link/EducacionMoneyTV",
        "🥳 Gracias por unirte a *MoneyTv*, *{{name}}*.\n\n👉 Aprende como vender tu producto en el siguiente link:\n👉zuum.link/EducacionMoneyTV",
        "😎 *{{name}}* Enhorabuena queremos darte la bienvenida en *MoneyTv*.\n\n👉 Aprende como vender tu producto en el siguiente link:\n 👉zuum.link/EducacionMoneyTV",
        "🥹 Genial *{{name}}* te has unido a *MoneyTv*.\n\n👉 Aprende como vender tu producto en el siguiente link:\n👉zuum.link/EducacionMoneyTV",
    ];

    const IPTV_SETUP_ARRAY = [
        "😁 *¡Hola {{name}}!* te enviamos tus datos de acceso a *MoneyTv*: \n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\nSi necesitas ayuda para el correcto para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario",
        "😊 *¡Gracias {{name}}!* ahora eres parte de *MoneyTv* estos son tus datos para iniciar sesión en tu servicio de IPTV: \n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\nSi necesitas ayuda para el correcto para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario",
        "😍 *¡Muchas gracias! {{name}}* ahora eres parte de *MoneyTv* estos son tus datos para iniciar sesión en tu servicio de IPTV: \n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\nSi necesitas ayuda para el correcto para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario",
    ];

    const getNewDemoMessage = [
        "😁 *¡Hola {{name}}!* te enviamos tus datos de acceso a *Funnel Millonario*: \n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\n👉 Si necesitas ayuda para el correcto para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario",
        "😊 *¡Gracias {{name}}!* ahora eres parte de *Funnel Millonario* estos son tus datos para iniciar sesión en tu servicio de IPTV: \n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\n👉 Si necesitas ayuda para el correcto para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario",
        "😍 *¡Muchas gracias! {{name}}* ahora eres parte de *Funnel Millonario* estos son tus datos para iniciar sesión en tu servicio de IPTV: \n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\n👉 Si necesitas ayuda para el correcto para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario",
    ];

    const IPTV_DEMO_SETUP_ARRAY = [
        "😁 *¡Hola {{name}}!* te enviamos tus datos de acceso a *MoneyTv*: \n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\nSi necesitas ayuda para el correcto funcionamiento para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario\n\nRecuerda que tu demo expira en 2 horas",
        "😎 *{{name}}*, aquí tienes tus datos de acceso a *MoneyTv*: \n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\nSi necesitas ayuda para el correcto funcionamiento para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario\n\nLa demo expira en 2 horas",
        "😊 *¡Tu demo {{name}}!* de *MoneyTv*. Estos son tus datos para iniciar sesión en tu servicio de IPTV: \n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\nSi necesitas ayuda para el correcto funcionamiento para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario\n\nTu demo expira en 2 horas",
        "😍 *¡La demo para *MoneyTV {{name}}*!  aquí está. Estos son tus datos para iniciar sesión en tu servicio de IPTV: \n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\nSi necesitas ayuda para el correcto funcionamiento para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario\n\nEsta demo expira en 2 horas",
    ];
    
    const IPTV_DEMO_TO_SERVICE_SETUP_ARRAY = [
        "😁 ¡Ahora tienes el servicio de MoneyTv *{{name}}*!. Tu usuario y contraseña es el mismo que utilizaste en tu demo\n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\nSi necesitas ayuda para el correcto funcionamiento para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario\n\nGracias por ser parte de MoneyTv",
        "😎 ¡Wow ya el servicio de MoneyTv *{{name}}*!. Tu usuario y contraseña es el mismo que utilizaste en tu demo\n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\nSi necesitas ayuda para el correcto funcionamiento para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario\n\nGracias ahora eres parte de MoneyTv",
        "🥹 ¡Genial *{{name}}* desde ahora ya tienes el servicio de MoneyTv!. Tu usuario y contraseña es el mismo que utilizaste en tu demo\n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\nSi necesitas ayuda para el correcto funcionamiento para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario\n\nGracias de parte del equipo MoneyTv",
        "😁 ¡Gracias *{{name}}*, ya hemos activado tu servicio MoneyTv!. Tu usuario y contraseña es el mismo que utilizaste en tu demo\n\n Usuario : *{{user_name}}* \n Contraseña : *{{client_password}}*\n\nSi necesitas ayuda para el correcto funcionamiento para SmarTv/Android/iPhone/iPad por favor da clic en: https://zuum.link/AyudaFunnelMillonario\n\nYa eres parte de MoneyTv",
    ];
    
    const IPTV_RENOVATION_ARRAY = [
        "😁 *¡Hola {{name}}!* ¡muchas gracias! hemos renovado tu acceso a *MoneyTv*: \n",
        "👋🏻 *¡Gracias! {{name}}!*. Tu renovación de *MoneyTv* ya está lista",
        "😍 *¡Enhorabuena! {{name}}!*. Ya está lista tu renovación de *MoneyTv*",
    ];

    const TICKET_DONE_ARRAY = [
        "😁 *¡Hola {{name}}!* hemos solucionado tu ticket *{{extra}}*: \n",
        "👋🏻 *¡Gracias {{name}}!* hemos dado solución a tu ticket *{{extra}}*: \n",
        "😁 *¡Hola {{name}}!* ya está resuelto tu ticket *{{extra}}*: \n",
    ];

    const TICKET_CREATED_ARRAY = [
        "😁 *¡Hola {{name}}!* hay un nuevo ticket *{{extra}}*: \n",
        "👋🏻 *¡Ey {{name}}!* nuevo ticket *{{extra}}*: \n",
        "😎 *¡Hola {{name}}!* se creó un ticket nuevo *{{extra}}*: \n",
    ];

    public static function getTicketDoneMessage()
    {
        return self::getRandomAnswer(self::TICKET_DONE_ARRAY);
    }

    public static function getTicketCreatedMessage()
    {
        return self::getRandomAnswer(self::TICKET_CREATED_ARRAY);
    }

    public static function getWelcomeMessage()
    {
        return self::getRandomAnswer(self::WELCOME_ARRAY);
    }

    public static function getRandomAnswer(array $answers = null)
    {
        return $answers[rand(0,sizeof($answers)-1)];
    }
    
    public static function getIptvSetUpMessage()
    {
        return self::getRandomAnswer(self::IPTV_SETUP_ARRAY);
    }
   
    public static function getIptvDemoToServiceSetUpMessage()
    {
        return self::getRandomAnswer(self::IPTV_DEMO_TO_SERVICE_SETUP_ARRAY);
    }

    public static function getIptvRenovationMessage()
    {
        return self::getRandomAnswer(self::IPTV_RENOVATION_ARRAY);
    }
    
    public static function getIptvSetUpDemoMessage()
    {
        return self::getRandomAnswer(self::IPTV_DEMO_SETUP_ARRAY);
    }
    
    public static function getNewDemoMessage()
    {
        return "¡Hola!, tenemos un demo pendiente en MoneyTV para {{name}}";
    }
   
    public static function getNewServiceMessage()
    {
        return "¡Hola!, tenemos un servicio pendiente en MoneyTV para {{name}}";
    }

    public static function getRenovationMessage()
    {
        return "¡Hola!, tenemos una renovación pendiente en MoneyTV para {{name}}";
    }
}
