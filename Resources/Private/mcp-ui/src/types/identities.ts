/**
 * Identity type definitions
 */

export interface Identity {
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

export interface IdentitiesByParty {
    party: {
        name: string;
    };
    identities: Identity[];
}

export interface CreateIdentityData {
    name: string;
}

export interface UpdateIdentityData {
    name: string;
}
