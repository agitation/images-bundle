ag.api.Object.register({
    "admin.v1/Picture": {
        "props": {
            "data": {
                "type": "string"
            },
            "description": {
                "type": "multilangstring",
                "maxLength": 150
            },
            "type": {
                "type": "string",
                "readonly": true
            },
            "width": {
                "type": "integer",
                "readonly": true
            },
            "height": {
                "type": "integer",
                "readonly": true
            },
            "fingerprint": {
                "type": "string",
                "readonly": true
            },
            "id": {
                "type": "integer",
                "nullable": true
            }
        }
    }
});
