<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-social-auth
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

use open20\amos\core\migration\AmosMigrationTableCreation;

/**
 * Class m180920_103032_create_idm_table
 */
class m180920_103032_create_idm_table extends AmosMigrationTableCreation
{
    /**
     * @inheritdoc
     */
    protected function setTableName()
    {
        $this->tableName = '{{%social_idm_user}}';
    }

    /**
     * @inheritdoc
     */
    protected function setTableFields()
    {
        $this->tableFields = [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->null()->comment('User ID'),
            'numeroMatricola' => $this->string(255)->notNull()->comment('Numero di Matricola'),
            'codiceFiscale' => $this->string(255)->null()->comment('Codice Fiscale'),
            'nome' => $this->string(255)->null()->comment('Nome'),
            'cognome' => $this->string(255)->null()->comment('Cognome'),
            'ssoDN' => $this->string(255)->null()->comment('ssoDN'),
            'tipoAutenticazione' => $this->string(255)->null()->comment('Tipo di Autenticazione'),
            'ssoUrlLogout' => $this->string(255)->null()->comment('Url di Logout SSO'),
            'responseBase64' => $this->string(255)->null()->comment('Risposta BASE64'),
            'emailAddress' => $this->string(255)->null()->comment('Indirizzo Email'),
            'livelloVerifica' => $this->string(255)->null()->comment('Livello di Verifica'),
            'nomeUtente' => $this->string(255)->null()->comment('Nome Utente'),
            'sesso' => $this->string(255)->null()->comment('Sesso'),
            'dataNascita' => $this->string(255)->null()->comment('Data di Nascita'),
            'luogoNascita' => $this->string(255)->null()->comment('Luogo di Nascita'),
            'provinciaNascita' => $this->string(255)->null()->comment('Provincia di Nascita'),
            'statoNascita' => $this->string(255)->null()->comment('Stato di Nascita'),
            'identificativoUtente' => $this->string(255)->null()->comment('Identificativo Utente'),
            'cellulare' => $this->string(255)->null()->comment('Cellulare'),
            'ragioneSociale' => $this->string(255)->null()->comment('Ragione Sociale'),
            'sedeLegale' => $this->string(255)->null()->comment('Sede Legale'),
            'partitaIVA' => $this->string(255)->null()->comment('Partita IVA'),
            'docIdentita' => $this->string(255)->null()->comment('Documento di Identita'),
            'scadDocIdentita' => $this->string(255)->null()->comment('Scadenza Documento'),
            'domicilioFisico' => $this->string(255)->null()->comment('Domicilio Fisico'),
            'domicilioDigitale' => $this->string(255)->null()->comment('Domicilio Digitale'),
            'origineDatiUtente' => $this->string(255)->null()->comment('Origina Dati'),
            'statoValidazioneProfiloUtente' => $this->string(255)->null()->comment('Stato di Validazione Profilo'),
            'idComuneRegistrazione' => $this->string(255)->null()->comment('ID Comune di Registrazione')
        ];
    }
}
