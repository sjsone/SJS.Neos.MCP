/**
 * Connection type definitions
 */

export interface Connection {
    __identity: string;
    name: string;
    token: string;
    createdAt: string;
    account: {
        accountIdentifier: string;
        authenticationProviderName: string;
    };
    party?: {
        name: string;
    };
}

export interface ConnectionsByParty {
    party: {
        name: string;
    };
    connections: Connection[];
}

export interface CreateConnectionData {
    name: string;
}

export interface UpdateConnectionData {
    name: string;
}
