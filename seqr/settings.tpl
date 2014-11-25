<form action="{$action}" method="post">
    <fieldset>
        <legend><img src="../modules/seqr/logo.gif" /> {l s='SEQR settings' mod='seqr'}</legend>
        <table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
            <tr>
                <td width="130" style="height: 35px;">{$data.userId.label}</td>
                <td>
                    <input type="text" name="{$data.userId.name}" value="{$data.userId.value}" style="width: 300px;" />
                </td>
            </tr>
            <tr>
                <td width="130" style="height: 35px;">{$data.terminalId.label}</td>
                <td>
                    <input type="text" name="{$data.terminalId.name}" value="{$data.terminalId.value}" style="width: 300px;" />
                </td>
            </tr>
            <tr>
                <td width="130" style="height: 35px;">{$data.terminalPass.label}</td>
                <td>
                    <input type="password" name="{$data.terminalPass.name}" value="{$data.terminalPass.value}" style="width: 300px;" />
                </td>
            </tr>
            <tr>
                <td width="130" style="height: 35px;">{$data.wsdlUrl.label}</td>
                <td>
                    <input type="text" name="{$data.wsdlUrl.name}" value="{$data.wsdlUrl.value}" style="width: 300px;" />
                </td>
            </tr>
            <tr>
                <td width="130" style="height: 35px;">{$data.timeout.label}</td>
                <td>
                    <input type="text" name="{$data.timeout.name}" value="{$data.timeout.value}" style="width: 300px;" />
                </td>
            </tr>

            <tr><td colspan="2" align="center"><input class="button" name="submit" value="{l s='Update settings' mod='seqr'}" type="submit" /></td></tr>
        </table>
    </fieldset>
</form>
