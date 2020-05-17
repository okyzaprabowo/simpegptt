@echo off
set varMode=%1
set varProjectCode=%2
set optCore=prod
set optPackage=prod
set optProject=prod

SHIFT && SHIFT

::pastikan parameter PROJECT CODE disi
IF %varMode%=="" (
    echo Parameter MODE belum diisi !
    echo init.bat [MODE:dev|prod|create] [PROJECT CODE]
    echo Setup Failed !
    EXIT /B
) ELSE (
    IF NOT %varMode%==dev (IF NOT %varMode%==prod (IF NOT %varMode%==create (
        echo parameter MODE keliru, hanya ada dev, prod atau create
        echo Setup Failed !
        EXIT /B
    )))
)

::pastikan parameter PROJECT CODE disi
IF %varProjectCode%=="" (
    echo Parameter PROJECT CODE belum diisi !
    echo Setup Failed !
    EXIT /B
)

::pastikan proses init dilakukan hanya saat pertama kali setup saja (app/Modules/System terinisiasi)
IF EXIST .\app\Modules\System (
    echo System telah diinisiasi
    EXIT /B
)

::copy composer.json
IF NOT EXIST composer.json (
    cp composer.json.default composer.json
)

composer install

::jika selain mode production maka detect option
IF NOT %varMode%==prod (
    :loop
    IF NOT "%~1"=="" (
        IF "%~1"=="--coreEnvMode" (
            SET optCore=%2
            SHIFT
        )
        IF "%~1"=="--packageEnvMode" (
            SET optPackage=%2
            SHIFT
        )
        IF "%~1"=="--projectEnvMode" (
            SET optProject=%2
            SHIFT
        )
        SHIFT
        GOTO :loop
    )

    echo Detecting apps-core
    IF EXIST ..\apps-core (
        echo Yes
    ) ELSE (
        echo No
    )

    IF %varMode%==dev (
        echo Mode Development
        EXIT /B
    )

    IF %varMode%==create (
        echo Mode Create New Project

    ) 
    EXIT /B
) 


echo Mode Deploy Production
    
